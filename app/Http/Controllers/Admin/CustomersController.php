<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Customer;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index(Request $request)
    {
        $query = Customer::withCount(['orders', 'orders as total_spent' => function($q) {
            $q->select(\DB::raw('coalesce(sum(total), 0)'));
        }])->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->appends($request->query());

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.payments', 'orders.items']);

        return view('admin.customers.show', compact('customer'));
    }

    public function syncWithGateway(Customer $customer)
    {
        try {
            if (!$customer->gateway_client_id) {
                $this->gateSDKService->createClient($customer);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer synchronized with Gateway successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}