@extends('layouts.app')

@section('title', 'Customers Management')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #328a75;">Admin</a></li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #3b4151;">
                <i class="bi bi-people me-2"></i>Customers Management
            </h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, phone..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Customers ({{ $customers->total() }})</h6>
                @if(request()->has('search'))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear Search
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Gateway Sync</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>
                                        <strong>{{ \App\Helpers\GdprHelper::maskName($customer->first_name, $customer->last_name) }}</strong><br>
                                        <small class="text-muted">ID: {{ $customer->id }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="bi bi-envelope me-1"></i>{{ \App\Helpers\GdprHelper::maskEmail($customer->email) }}<br>
                                            <i class="bi bi-phone me-1"></i>{{ \App\Helpers\GdprHelper::maskPhone($customer->phone) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $customer->orders_count }} orders</span>
                                    </td>
                                    <td>
                                        <strong>€{{ number_format($customer->total_spent, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($customer->gateway_client_id)
                                            <span class="badge bg-success">Synced</span><br>
                                            <small class="text-muted">{{ Str::limit($customer->gateway_client_id, 12) }}</small>
                                        @else
                                            <span class="badge bg-warning">Not Synced</span><br>
                                            <button onclick="syncCustomer({{ $customer->id }})" class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="bi bi-arrow-repeat me-1"></i>Sync
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $customer->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $customer->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No Customers Found</h5>
                    <p class="text-muted">
                        @if(request()->has('search'))
                            Try adjusting your search criteria.
                        @else
                            Customers will appear here once they register or place orders.
                        @endif
                    </p>
                </div>
            @endif
        </div>
        @if($customers->hasPages())
            <div class="card-footer bg-white">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function syncCustomer(customerId) {
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;

    button.innerHTML = '<i class="bi bi-arrow-repeat spin me-1"></i>Syncing...';
    button.disabled = true;

    fetch(`/admin/customers/${customerId}/sync`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Customer Synced',
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
            text: error.message
        });
    })
    .finally(() => {
        button.innerHTML = originalHtml;
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