@extends('layouts.app')

@section('title', 'Customer: ' . \App\Helpers\GdprHelper::maskName($customer->first_name, $customer->last_name))

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #328a75;">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" style="color: #328a75;">Customers</a></li>
                    <li class="breadcrumb-item active">{{ \App\Helpers\GdprHelper::maskName($customer->first_name, $customer->last_name) }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #3b4151;">
                <i class="bi bi-person me-2"></i>{{ \App\Helpers\GdprHelper::maskName($customer->first_name, $customer->last_name) }}
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Customer Details -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong><br>
                        {{ \App\Helpers\GdprHelper::maskName($customer->first_name, $customer->last_name) }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        {{ \App\Helpers\GdprHelper::maskEmail($customer->email) }}
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        {{ \App\Helpers\GdprHelper::maskPhone($customer->phone) }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer ID:</strong><br>
                        {{ $customer->id }}
                    </div>
                    @if($customer->gateway_client_id)
                        <div class="mb-3">
                            <strong>Gateway Client ID:</strong><br>
                            <code>{{ $customer->gateway_client_id }}</code>
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Registered:</strong><br>
                        {{ $customer->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order History ({{ $customer->orders->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    @if($customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Items</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'paid' => 'success',
                                                        'failed' => 'danger',
                                                        'cancelled' => 'secondary',
                                                        'issued' => 'info'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>€{{ number_format($order->total, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $methodNames = [
                                                        'card' => 'Credit Card',
                                                        'apple_pay' => 'Apple Pay',
                                                        'google_pay' => 'Google Pay',
                                                        'paypal' => 'PayPal',
                                                        'klarna' => 'Klarna'
                                                    ];
                                                @endphp
                                                {{ $methodNames[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>
                                                {{ $order->items->count() }} items
                                            </td>
                                            <td>
                                                {{ $order->created_at->format('M d, Y') }}<br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-sm" title="View Order">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2"><strong>Total Orders:</strong></td>
                                        <td><strong>€{{ number_format($customer->orders->sum('total'), 2) }}</strong></td>
                                        <td colspan="4"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No Orders</h5>
                            <p class="text-muted">This customer hasn't placed any orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection