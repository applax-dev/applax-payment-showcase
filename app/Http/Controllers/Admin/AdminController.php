<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Models\Shop\Customer;
use App\Models\Shop\Product;
use App\Models\Payment\Payment;
use App\Models\Payment\WebhookLog;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function dashboard()
    {
        // Get overview statistics
        $stats = [
            'orders' => [
                'total' => Order::count(),
                'today' => Order::whereDate('created_at', today())->count(),
                'pending' => Order::where('status', 'pending')->count(),
                'paid' => Order::where('status', 'paid')->count(),
                'failed' => Order::where('status', 'failed')->count(),
            ],
            'customers' => [
                'total' => Customer::count(),
                'today' => Customer::whereDate('created_at', today())->count(),
                'with_gateway' => Customer::whereNotNull('gateway_client_id')->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('status', 'active')->count(),
                'with_gateway' => Product::whereNotNull('gateway_product_id')->count(),
            ],
            'payments' => [
                'total' => Payment::count(),
                'completed' => Payment::where('status', 'completed')->count(),
                'failed' => Payment::where('status', 'failed')->count(),
                'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            ],
            'webhooks' => [
                'total' => WebhookLog::count(),
                'today' => WebhookLog::whereDate('created_at', today())->count(),
                'processed' => WebhookLog::where('status', 'processed')->count(),
                'failed' => WebhookLog::where('status', 'failed')->count(),
            ]
        ];

        // Get recent orders
        $recentOrders = Order::with(['customer', 'payments'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get() ?? collect();

        // Get payment method analytics
        $paymentMethods = Payment::select('method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('method')
            ->orderBy('count', 'desc')
            ->get() ?? collect();

        // Get orders by status for chart
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get daily order trends (last 7 days)
        $dailyOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(total) as amount')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get() ?? collect();

        // Get recent webhook logs
        $recentWebhooks = WebhookLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get() ?? collect();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'paymentMethods',
            'ordersByStatus',
            'dailyOrders',
            'recentWebhooks'
        ));
    }
}