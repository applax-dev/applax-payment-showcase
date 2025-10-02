@extends('layouts.app')

@section('title', 'Checkout - Review Order')

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none" style="color: #328a75;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}" class="text-decoration-none" style="color: #328a75;">Shop</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.cart.index') }}" class="text-decoration-none" style="color: #328a75;">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Checkout Progress -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Customer Info</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Payment Method</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #328a75; color: white;">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <small class="fw-bold" style="color: #328a75;">Review Order</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #e9ecef; color: #6c757d;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-muted">Complete</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-shopping-cart me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                    <div class="row align-items-center border-bottom pb-3 mb-3">
                        <div class="col-md-2">
                            @if($item['product']->image)
                            <img src="{{ $item['product']->image }}"
                                 alt="{{ $item['product']->name }}"
                                 class="img-fluid rounded"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                            <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-image fa-2x"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $item['product']->name }}</h6>
                            <p class="text-muted mb-1" style="font-size: 0.9rem;">{{ Str::limit($item['product']->description, 100) }}</p>
                            <span class="badge bg-secondary">{{ $item['product']->currency }}</span>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="fw-bold">Qty: {{ $item['quantity'] }}</span>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="fw-bold">€{{ number_format($item['product']->price, 2) }}</div>
                            <div class="text-muted" style="font-size: 0.9rem;">each</div>
                            <div class="fw-bold" style="color: #328a75;">
                                €{{ number_format($item['product']->price * $item['quantity'], 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-user me-2"></i>Customer Information
                        <a href="{{ route('shop.checkout.step', ['step' => 'customer']) }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>{{ $customerData['first_name'] }} {{ $customerData['last_name'] }}</strong><br>
                            <i class="fas fa-envelope me-2 text-muted"></i>{{ $customerData['email'] }}<br>
                            <i class="fas fa-phone me-2 text-muted"></i>{{ $customerData['phone'] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-credit-card me-2"></i>Payment Method
                        <a href="{{ route('shop.checkout.step', ['step' => 'payment']) }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @php
                            $paymentMethodInfo = [
                                'card' => ['icon' => 'fas fa-credit-card', 'name' => 'Credit/Debit Card', 'desc' => 'Secure card payment'],
                                'apple_pay' => ['icon' => 'fab fa-apple', 'name' => 'Apple Pay', 'desc' => 'Touch ID or Face ID'],
                                'google_pay' => ['icon' => 'fab fa-google', 'name' => 'Google Pay', 'desc' => 'Fast and secure'],
                                'paypal' => ['icon' => 'fab fa-paypal', 'name' => 'PayPal', 'desc' => 'PayPal account'],
                                'klarna' => ['icon' => 'fas fa-calendar-alt', 'name' => 'Klarna', 'desc' => 'Buy now, pay later']
                            ];
                            $method = $paymentMethodInfo[$paymentMethod] ?? $paymentMethodInfo['card'];
                        @endphp

                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 50px; height: 50px; background-color: #f8f9fa; color: #328a75;">
                            <i class="{{ $method['icon'] }} fa-lg"></i>
                        </div>
                        <div>
                            <strong>{{ $method['name'] }}</strong><br>
                            <small class="text-muted">{{ $method['desc'] }}</small>
                            @if($paymentMethod === 'card')
                                <br>
                                <a href="https://docs.appla-x.com/Integration/testing-and-going-live" target="_blank" class="text-decoration-none" style="color: #328a75; font-size: 0.85rem;">
                                    <i class="fas fa-credit-card me-1"></i>View test card numbers
                                </a>
                            @endif
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-success">Demo Mode</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary for Review -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Subtotal ({{ array_sum(array_column($cartItems, 'quantity')) }} items):</span>
                        <span>€{{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">Free</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tax:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Processing Fee:</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total:</h5>
                        <h4 class="mb-0" style="color: #328a75;">€{{ number_format($cartTotal, 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" style="color: #328a75;" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" style="color: #328a75;">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <!-- Demo Notice -->
            <div class="alert alert-warning mt-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Demo Environment:</strong> This is a demonstration checkout. No real payment will be processed and no real order will be fulfilled.
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">Complete Order</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="mb-0" style="color: #328a75;">€{{ number_format($cartTotal, 2) }}</h3>
                        <small class="text-muted">Total Amount</small>
                    </div>

                    <button type="button" class="btn btn-lg w-100 text-white mb-3"
                            style="background-color: #328a75; border-color: #328a75;"
                            id="placeOrderBtn" disabled>
                        <i class="fas fa-shield-alt me-2"></i>
                        <span id="btnText">Place Secure Order</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>

                    <a href="{{ route('shop.checkout.step', ['step' => 'payment']) }}" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="fas fa-arrow-left me-2"></i>Back to Payment
                    </a>

                    <div class="text-center">
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-lock me-1"></i>
                            Your payment is secured with SSL encryption
                        </small>
                        <div class="d-flex justify-content-center">
                            <i class="fab fa-cc-visa me-2" style="font-size: 1.5rem; color: #1a1f71;"></i>
                            <i class="fab fa-cc-mastercard me-2" style="font-size: 1.5rem; color: #eb001b;"></i>
                            <i class="fab fa-cc-amex me-2" style="font-size: 1.5rem; color: #006fcf;"></i>
                            <i class="fab fa-paypal me-2" style="font-size: 1.5rem; color: #003087;"></i>
                            <i class="fab fa-apple me-2" style="font-size: 1.5rem; color: #000;"></i>
                            <i class="fab fa-google" style="font-size: 1.5rem; color: #4285f4;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel" style="color: #3b4151;">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Demo Terms and Conditions</h6>
                <p>This is a demonstration platform for the Applax Gate SDK. By proceeding with this demo checkout:</p>
                <ul>
                    <li>You understand this is not a real transaction</li>
                    <li>No real payment will be processed</li>
                    <li>No real products will be shipped</li>
                    <li>Demo data may be reset periodically</li>
                    <li>This platform is for evaluation purposes only</li>
                </ul>
                <p><strong>For real implementations:</strong> Please replace these terms with your actual terms and conditions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn text-white" style="background-color: #328a75;" data-bs-dismiss="modal" onclick="document.getElementById('terms').checked = true; updatePlaceOrderButton();">
                    I Agree
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const termsCheckbox = document.getElementById('terms');

    // Enable/disable place order button based on terms
    termsCheckbox.addEventListener('change', updatePlaceOrderButton);

    function updatePlaceOrderButton() {
        placeOrderBtn.disabled = !termsCheckbox.checked;
    }

    // Initialize button state
    updatePlaceOrderButton();

    placeOrderBtn.addEventListener('click', function() {
        if (!termsCheckbox.checked) {
            Swal.fire({
                icon: 'warning',
                title: 'Terms Required',
                text: 'Please accept the terms and conditions to continue.'
            });
            return;
        }

        // Show loading state
        placeOrderBtn.disabled = true;
        btnText.textContent = 'Processing Order...';
        btnSpinner.classList.remove('d-none');

        // Simulate processing delay for demo
        setTimeout(() => {
            processOrder();
        }, 2000);
    });

    function processOrder() {
        fetch('{{ route("shop.checkout.process.order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                // Handle HTTP error responses
                return response.json().then(errorData => {
                    throw {
                        status: response.status,
                        data: errorData
                    };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.redirect_to_gateway && data.payment_url) {
                    // Redirect directly to Gateway payment page
                    window.location.href = data.payment_url;
                } else {
                    // Show success message for normal redirect
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed Successfully!',
                        text: 'Redirecting to confirmation page...',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                }
            } else {
                throw { data: data };
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Handle detailed error responses
            if (error.data && error.data.details) {
                Swal.fire({
                    icon: 'error',
                    title: error.data.message || 'Payment Gateway Configuration Error',
                    html: `<div style="text-align: left; white-space: pre-line; font-family: monospace; font-size: 0.9em; background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">${error.data.details}</div>`,
                    width: '700px',
                    confirmButtonText: 'Understand',
                    confirmButtonColor: '#328a75'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Order Failed',
                    text: (error.data && error.data.message) || error.message || 'An error occurred while processing your order. Please try again.',
                    confirmButtonColor: '#328a75'
                });
            }
        })
        .finally(() => {
            // Reset button state
            placeOrderBtn.disabled = false;
            btnText.textContent = 'Place Secure Order';
            btnSpinner.classList.add('d-none');
            updatePlaceOrderButton(); // Recheck terms
        });
    }

    // Make updatePlaceOrderButton globally accessible for modal
    window.updatePlaceOrderButton = updatePlaceOrderButton;
});
</script>
@endsection
