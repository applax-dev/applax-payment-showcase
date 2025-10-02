@extends('layouts.app')

@section('title', 'Shop - Payment Gateway Products')

@section('content')
<!-- Header -->
<section class="bg-light py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-1 text-primary">
                    <i class="bi bi-shop me-2"></i>Payment Gateway Solutions
                </h1>
                <p class="text-muted mb-0">
                    Explore our comprehensive payment integration products and services
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex align-items-center justify-content-lg-end">
                    <span class="text-muted me-2">{{ $products->total() }} products</span>
                    <span class="demo-badge">DEMO</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filter Products
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('shop.products.index') }}" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label small text-muted">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="Search products...">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label small text-muted">Price Range (EUR)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="price_min" value="{{ request('price_min') }}"
                                           placeholder="Min" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="price_max" value="{{ request('price_max') }}"
                                           placeholder="Max" min="0" step="0.01">
                                </div>
                            </div>
                            <small class="text-muted">
                                Range: €{{ number_format($priceStats->min_price, 2) }} - €{{ number_format($priceStats->max_price, 2) }}
                            </small>
                        </div>

                        <!-- Currency -->
                        @if($currencies->count() > 1)
                        <div class="mb-3">
                            <label for="currency" class="form-label small text-muted">Currency</label>
                            <select class="form-select form-select-sm" id="currency" name="currency">
                                <option value="">All Currencies</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}" {{ request('currency') === $currency ? 'selected' : '' }}>
                                        {{ strtoupper($currency) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-funnel me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('shop.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SDK Info -->
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-code-slash me-2"></i>SDK Integration
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">
                        Each product demonstrates different aspects of the gate-sdk integration:
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Product catalog management</li>
                        <li>Shopping cart functionality</li>
                        <li>Order processing</li>
                        <li>Multiple payment methods</li>
                        <li>Real-time webhooks</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            @if(request()->hasAny(['search', 'price_min', 'price_max', 'currency']))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Showing filtered results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                    ({{ $products->total() }} {{ Str::plural('product', $products->total()) }})
                    <a href="{{ route('shop.products.index') }}" class="alert-link ms-2">Clear filters</a>
                </div>
            @endif

            @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 payment-method-card">
                            <div class="position-relative">
                                <img src="{{ $product->image }}"
                                     alt="{{ $product->name }}"
                                     class="card-img-top"
                                     style="height: 200px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-primary">{{ strtoupper($product->currency) }}</span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary">{{ $product->name }}</h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    {{ Str::limit($product->description, 100) }}
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="h4 text-primary mb-0">
                                            €{{ number_format($product->price, 2) }}
                                        </div>
                                        <small class="text-muted">{{ $product->currency }}</small>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="button"
                                                class="btn btn-primary add-to-cart-btn"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-product-price="{{ $product->price }}">
                                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                        </button>
                                        <a href="{{ route('shop.products.show', $product) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No products found</h3>
                    <p class="text-muted mb-4">
                        Try adjusting your search criteria or
                        <a href="{{ route('shop.products.index') }}" class="text-primary">browse all products</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success me-2"></i>Added to Cart
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-cart-check display-6 text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 id="modal-product-name" class="mb-1"></h6>
                        <p class="text-muted mb-0">has been added to your cart</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Continue Shopping
                </button>
                <a href="{{ route('shop.cart.index') }}" class="btn btn-primary">
                    <i class="bi bi-cart me-2"></i>View Cart
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
            this.disabled = true;

            // Make AJAX request
            fetch(`{{ url('/shop/products') }}/${productId}/add-to-cart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantity: 1
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response content-type:', response.headers.get('content-type'));

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Server error response:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response received:', text);
                        throw new Error('Server returned non-JSON response');
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update cart count in navbar
                    const cartCountBadge = document.querySelector('.navbar .badge');
                    if (cartCountBadge) {
                        cartCountBadge.textContent = data.cart_count;
                    } else {
                        // Add badge if it doesn't exist
                        const cartLink = document.querySelector('.navbar a[href*="cart"]');
                        if (cartLink) {
                            cartLink.innerHTML += ' <span class="badge bg-primary rounded-pill">' + data.cart_count + '</span>';
                        }
                    }

                    // Show SweetAlert success
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: `${productName} has been added to your cart`,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        timer: 5000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while adding the product to cart.',
                    timer: 5000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            })
            .finally(() => {
                // Restore button
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Auto-submit form when filters change
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
@endpush