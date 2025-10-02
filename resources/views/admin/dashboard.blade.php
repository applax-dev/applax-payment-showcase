@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: #3b4151;">
            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
        </h1>
        <div class="d-flex align-items-center">
            <div class="me-3">
                <span class="badge bg-info">
                    <i class="bi bi-shield-check me-1"></i>GDPR Compliant
                </span>
                <small class="text-muted d-block">Personal data masked</small>
            </div>
            <div class="text-muted">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Orders Stats -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Total Orders</h6>
                            <h2 class="mb-0" style="color: #328a75;">{{ number_format($stats['orders']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-arrow-up text-success me-1"></i>
                                {{ $stats['orders']['today'] }} today
                            </small>
                        </div>
                        <div class="bg-light rounded-circle p-3" style="background-color: rgba(50, 138, 117, 0.1) !important;">
                            <i class="bi bi-receipt" style="font-size: 1.5rem; color: #328a75;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Stats -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Customers</h6>
                            <h2 class="mb-0" style="color: #328a75;">{{ number_format($stats['customers']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-arrow-up text-success me-1"></i>
                                {{ $stats['customers']['today'] }} today
                            </small>
                        </div>
                        <div class="bg-light rounded-circle p-3" style="background-color: rgba(50, 138, 117, 0.1) !important;">
                            <i class="bi bi-people" style="font-size: 1.5rem; color: #328a75;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Revenue</h6>
                            <h2 class="mb-0" style="color: #328a75;">€{{ number_format($stats['payments']['total_amount'], 2) }}</h2>
                            <small class="text-muted">
                                {{ $stats['payments']['completed'] }} completed payments
                            </small>
                        </div>
                        <div class="bg-light rounded-circle p-3" style="background-color: rgba(50, 138, 117, 0.1) !important;">
                            <i class="bi bi-currency-euro" style="font-size: 1.5rem; color: #328a75;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Stats -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Products</h6>
                            <h2 class="mb-0" style="color: #328a75;">{{ number_format($stats['products']['total']) }}</h2>
                            <small class="text-muted">
                                {{ $stats['products']['active'] }} active
                            </small>
                        </div>
                        <div class="bg-light rounded-circle p-3" style="background-color: rgba(50, 138, 117, 0.1) !important;">
                            <i class="bi bi-box-seam" style="font-size: 1.5rem; color: #328a75;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0" style="color: #3b4151;">Recent Orders</h6>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentOrders) && count($recentOrders) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                                    #{{ $order->id }}
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ \App\Helpers\GdprHelper::maskName($order->customer->first_name, $order->customer->last_name) }}</strong><br>
                                                    <small class="text-muted">{{ \App\Helpers\GdprHelper::maskEmail($order->customer->email) }}</small>
                                                </div>
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
                                            <td>€{{ number_format($order->total, 2) }}</td>
                                            <td>
                                                @php
                                                    $paymentMethodNames = [
                                                        'card' => 'Credit Card',
                                                        'apple_pay' => 'Apple Pay',
                                                        'google_pay' => 'Google Pay',
                                                        'paypal' => 'PayPal',
                                                        'klarna' => 'Klarna'
                                                    ];
                                                @endphp
                                                {{ $paymentMethodNames[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status Chart & Payment Methods -->
        <div class="col-lg-4">
            <!-- Order Status Breakdown -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">Order Status</h6>
                </div>
                <div class="card-body">
                    @if($stats['orders']['total'] > 0)
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'secondary',
                                'issued' => 'info'
                            ];
                        @endphp
                        @foreach(['pending', 'paid', 'failed', 'cancelled'] as $status)
                            @if(isset($ordersByStatus[$status]) && $ordersByStatus[$status] > 0)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-{{ $statusColors[$status] ?? 'secondary' }} me-2"></div>
                                        <span>{{ ucfirst($status) }}</span>
                                    </div>
                                    <span class="fw-bold">{{ $ordersByStatus[$status] }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No orders to display</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">Payment Methods</h6>
                </div>
                <div class="card-body">
                    @if(isset($paymentMethods) && count($paymentMethods) > 0)
                        @foreach($paymentMethods as $method)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    @php
                                        $methodNames = [
                                            'card' => 'Credit Card',
                                            'apple_pay' => 'Apple Pay',
                                            'google_pay' => 'Google Pay',
                                            'paypal' => 'PayPal',
                                            'klarna' => 'Klarna'
                                        ];
                                    @endphp
                                    <strong>{{ $methodNames[$method->method] ?? ucfirst($method->method) }}</strong><br>
                                    <small class="text-muted">{{ $method->count ?? 0 }} payments</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">€{{ number_format($method->total ?? 0, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No completed payments</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-receipt me-2"></i>Manage Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people me-2"></i>View Customers
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-box-seam me-2"></i>Manage Products
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.webhooks.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-arrow-left-right me-2"></i>Webhook Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
</style>
@endsection