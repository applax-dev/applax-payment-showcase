<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of orders for demo purposes
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by order ID or customer email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('email', 'LIKE', "%{$search}%")
                                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                                  ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20);

        // Get status counts for filter tabs
        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::whereIn('status', ['draft', 'issued', 'viewed'])->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'failed' => Order::whereIn('status', ['failed', 'cancelled', 'expired', 'rejected'])->count(),
        ];

        return view('shop.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'items.product', 'payments'])
            ->findOrFail($id);

        return view('shop.orders.show', compact('order'));
    }

    /**
     * Retry a failed order (for demo purposes)
     */
    public function retry($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->canBePaid()) {
            return redirect()->back()
                ->with('error', 'This order cannot be retried.');
        }

        // If order has existing payment URL, redirect directly to Gateway
        $paymentUrl = $order->getPaymentUrl();
        if ($paymentUrl) {
            return redirect($paymentUrl);
        }

        // Otherwise redirect to checkout with retry parameter
        return redirect()->route('shop.checkout.step', [
            'step' => 'payment',
            'retry_order' => $order->id
        ]);
    }
}
