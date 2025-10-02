<?php

namespace App\Jobs;

use App\Models\Shop\Order;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckOrderStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job - Check status of pending orders
     */
    public function handle(): void
    {
        Log::info('CheckOrderStatus job started');

        // Get all pending orders that have a gateway order ID
        $pendingOrders = Order::whereIn('status', ['draft', 'issued', 'viewed', 'in_progress'])
            ->whereNotNull('gateway_order_id')
            ->where('created_at', '>', now()->subHours(24)) // Only check orders from last 24 hours
            ->get();

        if ($pendingOrders->isEmpty()) {
            Log::info('No pending orders to check');
            return;
        }

        Log::info('Checking status for ' . $pendingOrders->count() . ' pending orders');

        $gateSDKService = new GateSDKService();
        $updatedCount = 0;

        foreach ($pendingOrders as $order) {
            try {
                // Get current status from Gateway
                $orderInfo = $gateSDKService->getOrderInfo($order->gateway_order_id);

                if (!$orderInfo) {
                    Log::warning("Could not retrieve order info for order {$order->id} (Gateway ID: {$order->gateway_order_id})");
                    continue;
                }

                $gatewayStatus = $orderInfo['status'] ?? null;

                // Only update if status actually changed
                if ($gatewayStatus && $gatewayStatus !== $order->status) {
                    Log::info("Order {$order->id} status changed from '{$order->status}' to '{$gatewayStatus}'");

                    $order->update(['status' => $gatewayStatus]);

                    // If order is now paid, mark it as paid and update customer
                    if ($gatewayStatus === 'paid' && !$order->isPaid()) {
                        $order->markAsPaid();
                        Log::info("Order {$order->id} marked as paid");
                    }

                    $updatedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Error checking status for order {$order->id}: " . $e->getMessage());
            }
        }

        Log::info("CheckOrderStatus job completed. Updated {$updatedCount} orders");
    }
}