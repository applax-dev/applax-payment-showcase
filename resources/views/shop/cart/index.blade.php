@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<!-- Header -->
<section class="bg-light py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-1 text-primary">
                    <i class="bi bi-cart me-2"></i>Shopping Cart
                </h1>
                <p class="text-muted mb-0">
                    Review your selected items and proceed to checkout
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="demo-badge">DEMO</span>
            </div>
        </div>
    </div>
</section>

<div class="container py-4">
    <!-- Removed Items Alert -->
    @if(!empty($removedItems))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Notice:</strong> {{ count($removedItems) }} item(s) were removed from your cart because they are no longer available.
            <ul class="mb-0 mt-2">
                @foreach($removedItems as $item)
                    <li>{{ $item['name'] }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($cartSummary['is_empty'])
        <!-- Empty Cart -->
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                <h3 class="text-muted mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">
                    Looks like you haven't added any items to your cart yet. Start shopping to add items to your cart.
                </p>
                <a href="{{ route('shop.products.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-bag me-2"></i>Cart Items ({{ $cartSummary['count'] }})
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearCartBtn">
                            <i class="bi bi-trash me-1"></i>Clear Cart
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                            <div class="cart-item border-bottom p-4" data-product-id="{{ $item['id'] }}">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2 mb-3 mb-md-0">
                                        <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=150&h=100&fit=crop&crop=center' }}"
                                             class="img-fluid rounded"
                                             alt="{{ $item['name'] }}"
                                             style="height: 80px; width: 100%; object-fit: cover;">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <h6 class="mb-1">
                                            <a href="{{ route('shop.products.show', $item['product']) }}"
                                               class="text-decoration-none text-dark">
                                                {{ $item['name'] }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $item['formatted_price'] }}</small>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary quantity-btn"
                                                    type="button"
                                                    data-action="decrease"
                                                    data-product-id="{{ $item['id'] }}">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number"
                                                   class="form-control text-center quantity-input"
                                                   value="{{ $item['quantity'] }}"
                                                   min="1"
                                                   max="10"
                                                   data-product-id="{{ $item['id'] }}">
                                            <button class="btn btn-outline-secondary quantity-btn"
                                                    type="button"
                                                    data-action="increase"
                                                    data-product-id="{{ $item['id'] }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal & Remove -->
                                    <div class="col-md-2 mb-3 mb-md-0">
                                        <div class="text-end">
                                            <div class="fw-bold text-success item-subtotal">
                                                {{ $item['formatted_subtotal'] }}
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-link text-danger p-0 mt-1 remove-item-btn"
                                                    data-product-id="{{ $item['id'] }}"
                                                    title="Remove item">
                                                <i class="bi bi-x-lg"></i> Remove
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Actions (Mobile) -->
                                    <div class="col-md-1 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('shop.products.show', $item['product']) }}">
                                                        <i class="bi bi-eye me-2"></i>View Product
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger remove-item-btn"
                                                            data-product-id="{{ $item['id'] }}">
                                                        <i class="bi bi-trash me-2"></i>Remove
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="{{ route('shop.products.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-calculator me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Items Summary -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items ({{ $cartSummary['count'] }})</span>
                            <span id="cartSubtotal">{{ $cartSummary['formatted_total'] }}</span>
                        </div>

                        <!-- Shipping (Demo) -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span class="text-success">FREE</span>
                        </div>

                        <!-- Tax (Demo) -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax</span>
                            <span>Included</span>
                        </div>

                        <hr>

                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-success fs-5" id="cartTotal">{{ $cartSummary['formatted_total'] }}</span>
                        </div>

                        <!-- Promo Code (Demo) -->
                        <div class="mb-3">
                            <div class="input-group input-group-sm">
                                <input type="text"
                                       class="form-control"
                                       placeholder="Promo code"
                                       id="promoCode">
                                <button class="btn btn-outline-secondary" type="button" id="applyPromoBtn">
                                    Apply
                                </button>
                            </div>
                            <small class="text-muted">Demo: Promo codes not functional</small>
                        </div>

                        <!-- Checkout Button -->
                        <button type="button" class="btn btn-primary btn-lg w-100 mb-3" id="checkoutBtn">
                            <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                        </button>

                        <!-- Payment Methods Info -->
                        <div class="text-center">
                            <small class="text-muted">We accept:</small>
                            <div class="mt-2">
                                <i class="bi bi-credit-card fs-4 me-2 text-primary"></i>
                                <i class="bi bi-paypal fs-4 me-2 text-primary"></i>
                                <i class="bi bi-apple fs-4 me-2 text-primary"></i>
                                <i class="bi bi-google fs-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>Processing...</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

    // Quantity controls
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const productId = this.dataset.productId;
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let currentValue = parseInt(input.value);

            if (action === 'increase' && currentValue < 10) {
                currentValue++;
            } else if (action === 'decrease' && currentValue > 1) {
                currentValue--;
            }

            input.value = currentValue;
            updateQuantity(productId, currentValue);
        });
    });

    // Quantity input changes
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let quantity = parseInt(this.value);

            if (quantity < 1) quantity = 1;
            if (quantity > 10) quantity = 10;

            this.value = quantity;
            updateQuantity(productId, quantity);
        });
    });

    // Remove item buttons
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            removeItem(productId);
        });
    });

    // Clear cart button
    const clearCartBtn = document.getElementById('clearCartBtn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Clear Cart?',
                text: 'Are you sure you want to clear your entire cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    clearCart();
                }
            });
        });
    }

    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            proceedToCheckout();
        });
    }

    // Promo code button (demo)
    const applyPromoBtn = document.getElementById('applyPromoBtn');
    if (applyPromoBtn) {
        applyPromoBtn.addEventListener('click', function() {
            alert('Promo codes are not available in the demo version.');
        });
    }

    // Update quantity function
    async function updateQuantity(productId, quantity) {
        loadingModal.show();

        try {
            const response = await fetch('{{ route("shop.cart.update-quantity") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                updateCartDisplay(data.cart_summary);
                showToast('success', data.message);
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            console.error('Update quantity error:', error);
            showToast('error', 'An error occurred while updating the cart.');
        } finally {
            // Force hide modal with multiple fallback methods
            try {
                loadingModal.hide();

                // Ensure modal is properly closed after a brief delay
                setTimeout(() => {
                    const modalElement = document.getElementById('loadingModal');
                    if (modalElement.classList.contains('show')) {
                        modalElement.classList.remove('show');
                        modalElement.style.display = 'none';
                    }

                    // Clean up any leftover backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });

                    // Reset body styles
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            } catch (e) {
                console.error('Error hiding modal:', e);
                // Manual cleanup as ultimate fallback
                const modalElement = document.getElementById('loadingModal');
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }
    }

    // Remove item function
    async function removeItem(productId) {
        const result = await Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from your cart?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) return;

        loadingModal.show();

        try {
            const response = await fetch('{{ route("shop.cart.remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const data = await response.json();

            if (data.success) {
                // Remove item from DOM
                const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }

                updateCartDisplay(data.cart_summary);
                showToast('success', data.message);

                // Reload page if cart is empty
                if (data.cart_summary.is_empty) {
                    location.reload();
                }
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            console.error('Remove item error:', error);
            showToast('error', 'An error occurred while removing the item.');
        } finally {
            // Force hide modal with multiple fallback methods
            try {
                loadingModal.hide();

                // Ensure modal is properly closed after a brief delay
                setTimeout(() => {
                    const modalElement = document.getElementById('loadingModal');
                    if (modalElement.classList.contains('show')) {
                        modalElement.classList.remove('show');
                        modalElement.style.display = 'none';
                    }

                    // Clean up any leftover backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });

                    // Reset body styles
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            } catch (e) {
                console.error('Error hiding modal:', e);
                // Manual cleanup as ultimate fallback
                const modalElement = document.getElementById('loadingModal');
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }
    }

    // Clear cart function
    async function clearCart() {
        loadingModal.show();

        try {
            const response = await fetch('{{ route("shop.cart.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            showToast('error', 'An error occurred while clearing the cart.');
        } finally {
            loadingModal.hide();
        }
    }

    // Proceed to checkout function
    async function proceedToCheckout() {
        loadingModal.show();

        try {
            const response = await fetch('{{ route("shop.cart.proceed-to-checkout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Checkout error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                showToast('error', data.message || 'Checkout failed');
            }
        } catch (error) {
            console.error('Checkout error:', error);
            showToast('error', 'An error occurred while proceeding to checkout.');
        } finally {
            // Force hide modal with multiple fallback methods
            try {
                loadingModal.hide();

                // Ensure modal is properly closed after a brief delay
                setTimeout(() => {
                    const modalElement = document.getElementById('loadingModal');
                    if (modalElement.classList.contains('show')) {
                        modalElement.classList.remove('show');
                        modalElement.style.display = 'none';
                    }

                    // Clean up any leftover backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });

                    // Reset body styles
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            } catch (e) {
                console.error('Error hiding modal:', e);
                // Manual cleanup as ultimate fallback
                const modalElement = document.getElementById('loadingModal');
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }
    }

    // Update cart display
    function updateCartDisplay(cartSummary) {
        document.getElementById('cartSubtotal').textContent = cartSummary.formatted_total;
        document.getElementById('cartTotal').textContent = cartSummary.formatted_total;

        // Update navigation cart counter if exists
        const cartCounter = document.querySelector('.cart-counter');
        if (cartCounter) {
            cartCounter.textContent = cartSummary.count;
            if (cartSummary.count > 0) {
                cartCounter.classList.remove('d-none');
            } else {
                cartCounter.classList.add('d-none');
            }
        }
    }

    // Show SweetAlert notification
    function showToast(type, message) {
        if (type === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }
});
</script>
@endpush
@endsection