@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<!-- Breadcrumb -->
<section class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('shop.products.index') }}" class="text-decoration-none">
                        <i class="bi bi-shop me-1"></i>Products
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container py-4">
    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="position-relative">
                    <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop&crop=center' }}"
                         class="card-img-top"
                         alt="{{ $product->name }}"
                         style="height: 400px; object-fit: cover;">

                    <!-- Status Badge -->
                    <div class="position-absolute top-0 start-0 m-3">
                        @if($product->isActive())
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif
                    </div>

                    <!-- Demo Badge -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="demo-badge">DEMO</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="mb-4">
                <h1 class="h2 text-primary mb-3">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="mb-4">
                    <div class="d-flex align-items-baseline">
                        <span class="h3 text-success mb-0">
                            {{ $product->formatted_price }}
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5 class="text-primary mb-2">
                        <i class="bi bi-info-circle me-2"></i>Description
                    </h5>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>

                <!-- Product Features (Demo) -->
                <div class="mb-4">
                    <h5 class="text-primary mb-2">
                        <i class="bi bi-check-circle me-2"></i>Key Features
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Full SDK Integration
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Comprehensive Documentation
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            24/7 Technical Support
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            PCI DSS Compliant
                        </li>
                    </ul>
                </div>

                <!-- Add to Cart Form -->
                @if($product->isActive())
                    <form id="addToCartForm" class="mb-4">
                        @csrf
                        <div class="row g-2">
                            <div class="col-4">
                                <label for="quantity" class="form-label small text-muted">Quantity</label>
                                <select class="form-select" id="quantity" name="quantity">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-8 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This product is currently unavailable.
                    </div>
                @endif

                <!-- SDK Integration Info -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="bi bi-code-square me-2"></i>SDK Integration
                        </h6>
                        <p class="card-text small text-muted mb-2">
                            This product integrates with our Gate SDK for seamless payment processing.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('sdk.showcase') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View SDK Demo
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-file-earmark-code me-1"></i>Documentation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h3 class="text-primary mb-4">
                <i class="bi bi-collection me-2"></i>Related Products
            </h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <img src="{{ $relatedProduct->image ?: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=200&fit=crop&crop=center' }}"
                                 class="card-img-top"
                                 alt="{{ $relatedProduct->name }}"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    {{ Str::limit($relatedProduct->description, 80) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-success fw-bold">
                                        {{ $relatedProduct->formatted_price }}
                                    </span>
                                    <a href="{{ route('shop.products.show', $relatedProduct) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Success Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="cartSuccessToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle me-2"></i>
            <strong class="me-auto">Success!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Product added to cart successfully!
        </div>
    </div>
</div>

<!-- Error Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="cartErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong class="me-auto">Error!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="errorToastMessage">
            Something went wrong!
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.getElementById('addToCartForm');
    const successToast = new bootstrap.Toast(document.getElementById('cartSuccessToast'));
    const errorToast = new bootstrap.Toast(document.getElementById('cartErrorToast'));
    const errorToastMessage = document.getElementById('errorToastMessage');

    if (addToCartForm) {
        addToCartForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(addToCartForm);
            const submitBtn = addToCartForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }

                // Get quantity value
                const quantitySelect = addToCartForm.querySelector('#quantity');
                const quantity = quantitySelect ? quantitySelect.value : '1';

                const response = await fetch('{{ route("shop.products.add-to-cart", $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: parseInt(quantity)
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response content-type:', response.headers.get('content-type'));

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const responseText = await response.text();
                    console.error('Non-JSON response received:', responseText);
                    throw new Error('Server returned non-JSON response');
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show SweetAlert success
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });

                    // Update cart counter in navigation if exists
                    const cartCounter = document.querySelector('nav .badge');
                    if (cartCounter && data.cart_count !== undefined) {
                        cartCounter.textContent = data.cart_count;
                        if (data.cart_count > 0) {
                            cartCounter.classList.remove('d-none');
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to add product to cart',
                        timer: 5000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                errorToastMessage.textContent = 'Network error occurred';
                errorToast.show();
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});
</script>
@endpush
@endsection