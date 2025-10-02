@extends('layouts.app')

@section('title', 'Orders Management')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #328a75;">Admin</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #3b4151;">
                <i class="bi bi-receipt me-2"></i>Orders Management
            </h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Order ID, email, name..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $method)
                            @php
                                $methodNames = [
                                    'card' => 'Credit Card',
                                    'apple_pay' => 'Apple Pay',
                                    'google_pay' => 'Google Pay',
                                    'paypal' => 'PayPal',
                                    'klarna' => 'Klarna'
                                ];
                            @endphp
                            <option value="{{ $method }}" {{ request('payment_method') === $method ? 'selected' : '' }}>
                                {{ $methodNames[$method] ?? ucfirst($method) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Orders ({{ $orders->total() }})</h6>
                @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Items</th>
                                <th>Gateway ID</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->id }}</strong>
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
                                    <td>
                                        <strong>€{{ number_format($order->total, 2) }}</strong><br>
                                        <small class="text-muted">{{ $order->currency }}</small>
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
                                        <span class="badge bg-light text-dark">{{ $order->items->count() }} items</span>
                                    </td>
                                    <td>
                                        @if($order->gateway_order_id)
                                            <code class="small">{{ Str::limit($order->gateway_order_id, 12) }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($order->gateway_order_id)
                                                <button onclick="syncOrder({{ $order->id }})" class="btn btn-outline-secondary btn-sm" title="Sync Status">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
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
                    <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No Orders Found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                            Try adjusting your filter criteria.
                        @else
                            Orders will appear here once customers start placing them.
                        @endif
                    </p>
                </div>
            @endif
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function syncOrder(orderId) {
    const button = event.target.closest('button');
    const originalIcon = button.innerHTML;

    button.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i>';
    button.disabled = true;

    fetch(`/admin/orders/${orderId}/sync`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Synced',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Sync Failed',
                text: error.message || 'Failed to sync order status'
            });
        })
        .finally(() => {
            button.innerHTML = originalIcon;
            button.disabled = false;
        });
}
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endsection