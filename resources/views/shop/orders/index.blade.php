@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Orders Demo
                </h2>
                <div class="text-muted small">
                    <strong>MID:</strong> {{ config('services.gate.api_key') ? substr(config('services.gate.api_key'), 0, 8) . '...' : 'Not configured' }}
                    <span class="badge bg-{{ config('services.gate.sandbox') ? 'warning' : 'success' }} ms-2">
                        {{ config('services.gate.sandbox') ? 'Sandbox' : 'Production' }}
                    </span>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text"
                                   class="form-control"
                                   name="search"
                                   placeholder="Search by Order ID or Customer..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Orders</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="text-primary mb-1">{{ $statusCounts['all'] }}</h5>
                                <small class="text-muted">Total Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="text-warning mb-1">{{ $statusCounts['pending'] }}</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="text-success mb-1">{{ $statusCounts['paid'] }}</h5>
                                <small class="text-muted">Paid</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-danger mb-1">{{ $statusCounts['failed'] }}</h5>
                            <small class="text-muted">Failed</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orders List
                        @if($orders->total() > 0)
                            <span class="badge bg-secondary ms-2">{{ $orders->total() }} orders</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->id }}</strong>
                                                @if($order->gateway_order_id)
                                                    <br><small class="text-muted">{{ substr($order->gateway_order_id, 0, 8) }}...</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</div>
                                                <small class="text-muted">{{ $order->customer->email }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $order->formatted_total }}</span>
                                                <br><small class="text-muted">{{ $order->items->count() }} items</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->status_color }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-credit-card me-1"></i>
                                                {{ ucfirst($order->payment_method ?? 'Not set') }}
                                            </td>
                                            <td>
                                                <div>{{ $order->created_at->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('shop.orders.show', $order) }}"
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($order->canBePaid())
                                                        <a href="{{ route('shop.orders.retry', $order) }}"
                                                           class="btn btn-outline-warning">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">No Orders Found</h5>
                            <p class="text-muted mb-4">
                                @if(request('search') || request('status') !== 'all')
                                    No orders match your search criteria.
                                @else
                                    Start by creating your first order through the checkout process.
                                @endif
                            </p>
                            <a href="{{ route('shop.cart.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>

                @if($orders->hasPages())
                    <div class="card-footer">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection