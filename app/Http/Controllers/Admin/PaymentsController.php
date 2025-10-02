<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.customer'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('method') && $request->method) {
            $query->where('method', $request->method);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('gateway_payment_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('id', 'like', "%{$search}%")
                                 ->orWhere('gateway_order_id', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(20)->appends($request->query());

        // Get filter options
        $statuses = Payment::select('status')->distinct()->pluck('status')->filter()->sort()->values();
        $methods = Payment::select('method')->distinct()->pluck('method')->filter()->sort()->values();

        return view('admin.payments.index', compact('payments', 'statuses', 'methods'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.customer', 'order.items']);

        return view('admin.payments.show', compact('payment'));
    }
}