<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Models\Payment\PaymentTransaction;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'payments'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('gateway_order_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('email', 'like', "%{$search}%")
                                   ->orWhere('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20)->appends($request->query());

        // Get filter options
        $statuses = Order::select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->sort()
            ->values();

        $paymentMethods = Order::select('payment_method')
            ->distinct()
            ->pluck('payment_method')
            ->filter()
            ->sort()
            ->values();

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentMethods'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments.transactions']);

        // Try to get fresh order data from Gateway
        $gatewayOrderData = null;
        if ($order->gateway_order_id) {
            try {
                $gatewayOrderData = $this->gateSDKService->getOrderInfo($order->gateway_order_id);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch Gateway order data', [
                    'order_id' => $order->id,
                    'gateway_order_id' => $order->gateway_order_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('admin.orders.show', compact('order', 'gatewayOrderData'));
    }

    public function capturePayment(Order $order, Request $request)
    {
        try {
            $amount = $request->input('amount');

            $result = $this->gateSDKService->capturePayment($order, $amount);

            // Update order status
            $order->update(['status' => 'paid']);

            // Update payment record
            if ($order->payments()->exists()) {
                $payment = $order->payments()->latest()->first();
                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => array_merge(
                        is_array($payment->gateway_response) ? $payment->gateway_response : [],
                        ['capture' => $result, 'captured_at' => now()]
                    )
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment captured successfully',
                'amount' => $amount ?: $order->total
            ]);

        } catch (\Exception $e) {
            Log::error('Payment capture failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment capture failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refund(Order $order, Request $request)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:' . $order->total,
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $amount = $request->input('amount', $order->total);
            $reason = $request->input('reason', 'Admin refund');

            $result = $this->gateSDKService->refundPayment($order, $amount, $reason);

            // Update payment record and create transaction history
            if ($order->payments()->exists()) {
                $payment = $order->payments()->latest()->first();
                $currentRefunded = $payment->refunded_amount ?? 0;
                $newRefundedAmount = $currentRefunded + $amount;

                $payment->update([
                    'refunded_amount' => $newRefundedAmount,
                    'status' => $newRefundedAmount >= $order->total ? 'refunded' : 'partially_refunded',
                    'gateway_response' => array_merge(
                        is_array($payment->gateway_response) ? $payment->gateway_response : [],
                        ['refund' => $result, 'refunded_at' => now()]
                    )
                ]);

                // Create transaction record
                PaymentTransaction::createRefundTransaction(
                    $payment,
                    $amount,
                    $reason,
                    $result ?? []
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'amount' => $amount,
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Order $order)
    {
        try {
            $result = $this->gateSDKService->cancelOrder($order);

            // Update payment record
            if ($order->payments()->exists()) {
                $payment = $order->payments()->latest()->first();
                $payment->update([
                    'status' => 'cancelled',
                    'gateway_response' => array_merge(
                        is_array($payment->gateway_response) ? $payment->gateway_response : [],
                        ['cancel' => $result, 'cancelled_at' => now()]
                    )
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Order cancellation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Order cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncStatus(Order $order)
    {
        try {
            if (!$order->gateway_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order has no Gateway ID to sync'
                ], 400);
            }

            $gatewayData = $this->gateSDKService->getOrder($order);

            // Update order with fresh Gateway data
            $order->update([
                'status' => $gatewayData['status'] ?? $order->status,
                'gateway_data' => array_merge($order->gateway_data ?? [], $gatewayData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order status synchronized successfully',
                'status' => $gatewayData['status'] ?? $order->status
            ]);

        } catch (\Exception $e) {
            Log::error('Order sync failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Order sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}