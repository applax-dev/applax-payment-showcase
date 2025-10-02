<?php

namespace App\Console\Commands;

use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Payment\Payment;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupShowcaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'showcase:cleanup {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up showcase demo data - removes all orders, payments, and customers from database and Gateway';

    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        parent::__construct();
        $this->gateSDKService = $gateSDKService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Applax Showcase Cleanup Tool');
        $this->info('================================');
        $this->warn('⚠️  NOTE: Gateway orders cannot be deleted, only cancelled.');
        $this->warn('💡 TIP: Consider using a dedicated test API key for demos.');

        // Show current counts
        $orderCount = Order::count();
        $customerCount = Customer::count();
        $paymentCount = Payment::count();

        $this->table(['Type', 'Count'], [
            ['Orders', $orderCount],
            ['Customers', $customerCount],
            ['Payments', $paymentCount],
        ]);

        if ($orderCount === 0 && $customerCount === 0 && $paymentCount === 0) {
            $this->info('✅ Database is already clean!');
            return Command::SUCCESS;
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will DELETE ALL showcase data. Are you sure?')) {
                $this->info('Cleanup cancelled.');
                return self::INVALID;
            }
        }

        $this->info('🚀 Starting cleanup process...');

        return $this->performCleanup();
    }

    protected function performCleanup()
    {
        $errors = [];
        $stats = [
            'customers_deleted' => 0,
            'orders_deleted' => 0,
            'payments_deleted' => 0,
            'gateway_orders_cancelled' => 0,
            'gateway_clients_deleted' => 0,
            'gateway_errors' => 0,
        ];

        try {
            DB::beginTransaction();

            // Step 1: Clean up Gateway orders first (orders with gateway_order_id)
            $this->info('🔄 Cleaning Gateway orders...');
            $ordersWithGateway = Order::whereNotNull('gateway_order_id')->get();
            $gatewayOrdersDeleted = 0;

            foreach ($ordersWithGateway as $order) {
                try {
                    // Cancel the order first, then it should be cleanable
                    $this->gateSDKService->cancelOrderRaw($order->gateway_order_id);
                    $this->line("  ✅ Cancelled Gateway order: {$order->gateway_order_id}");
                    $gatewayOrdersDeleted++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to cancel Gateway order {$order->gateway_order_id}: " . $e->getMessage();
                    $stats['gateway_errors']++;
                    Log::warning('Gateway order cancellation failed', [
                        'order_id' => $order->id,
                        'gateway_order_id' => $order->gateway_order_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Step 2: Clean up Gateway clients (customers with gateway_client_id)
            $this->info('🔄 Cleaning Gateway clients...');
            $customersWithGateway = Customer::whereNotNull('gateway_client_id')->get();
            $gatewayClientsDeleted = 0;

            foreach ($customersWithGateway as $customer) {
                try {
                    $this->gateSDKService->deleteClientRaw($customer->gateway_client_id);
                    $this->line("  ✅ Deleted Gateway client: {$customer->gateway_client_id}");
                    $gatewayClientsDeleted++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete Gateway client {$customer->gateway_client_id}: " . $e->getMessage();
                    $stats['gateway_errors']++;
                    Log::warning('Gateway client deletion failed', [
                        'customer_id' => $customer->id,
                        'gateway_client_id' => $customer->gateway_client_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $stats['gateway_orders_cancelled'] = $gatewayOrdersDeleted;
            $stats['gateway_clients_deleted'] = $gatewayClientsDeleted;

            // Step 3: Clean up database tables (with foreign key constraints)
            $this->info('🔄 Cleaning database...');

            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Get counts before deletion
            $stats['payments_deleted'] = Payment::count();
            $stats['orders_deleted'] = Order::count();
            $stats['customers_deleted'] = Customer::count();

            // Truncate all tables in any order (foreign keys disabled)
            if (DB::getSchemaBuilder()->hasTable('payment_transactions')) {
                DB::table('payment_transactions')->truncate();
                $this->info("✅ Deleted payment transactions");
            }

            if (DB::getSchemaBuilder()->hasTable('payments')) {
                Payment::truncate();
                $this->info("✅ Deleted {$stats['payments_deleted']} payments");
            }

            if (DB::getSchemaBuilder()->hasTable('order_items')) {
                OrderItem::truncate();
                $this->info("✅ Deleted order items");
            }

            if (DB::getSchemaBuilder()->hasTable('orders')) {
                Order::truncate();
                $this->info("✅ Deleted {$stats['orders_deleted']} orders");
            }

            if (DB::getSchemaBuilder()->hasTable('customers')) {
                Customer::truncate();
                $this->info("✅ Deleted {$stats['customers_deleted']} customers");
            }

            // Step 4: Reset auto-increment counters
            if (DB::getSchemaBuilder()->hasTable('customers')) {
                DB::statement('ALTER TABLE customers AUTO_INCREMENT = 1');
            }
            if (DB::getSchemaBuilder()->hasTable('orders')) {
                DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');
            }
            if (DB::getSchemaBuilder()->hasTable('order_items')) {
                DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');
            }
            if (DB::getSchemaBuilder()->hasTable('payments')) {
                DB::statement('ALTER TABLE payments AUTO_INCREMENT = 1');
            }
            if (DB::getSchemaBuilder()->hasTable('payment_transactions')) {
                DB::statement('ALTER TABLE payment_transactions AUTO_INCREMENT = 1');
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            // Summary
            $this->info('');
            $this->info('🎉 Cleanup completed successfully!');
            $this->table(['Action', 'Count'], [
                ['Database customers deleted', $stats['customers_deleted']],
                ['Database orders deleted', $stats['orders_deleted']],
                ['Database payments deleted', $stats['payments_deleted']],
                ['Gateway orders cancelled', $stats['gateway_orders_cancelled']],
                ['Gateway clients deleted', $stats['gateway_clients_deleted']],
                ['Gateway errors', $stats['gateway_errors']],
            ]);

            if (!empty($errors)) {
                $this->warn('⚠️  Some errors occurred:');
                foreach ($errors as $error) {
                    $this->error("  • {$error}");
                }
            }

            $this->info('');
            if ($stats['gateway_orders_cancelled'] > 0 || $stats['gateway_clients_deleted'] > 0) {
                $this->info('✅ Gateway cleanup completed - orders cancelled and clients deleted');
                $this->warn('⚠️  Cancelled orders remain in Gateway for audit purposes');
            }

            $this->info('🚀 Local database is clean and ready for fresh demos!');
            $this->info('');
            $this->comment('💡 For production demos, consider:');
            $this->comment('   • Using a dedicated test merchant account');
            $this->comment('   • Requesting Gateway support for test data cleanup');
            $this->comment('   • Periodically rotating demo API keys');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            Log::error('Showcase cleanup failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
