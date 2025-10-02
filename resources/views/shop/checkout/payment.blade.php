@extends('layouts.app')

@section('title', 'Checkout - Payment Method')

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
                                     style="width: 40px; height: 40px; background-color: #328a75; color: white;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <small class="fw-bold" style="color: #328a75;">Payment Method</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #e9ecef; color: #6c757d;">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <small class="text-muted">Review Order</small>
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-credit-card me-2"></i>Payment Method
                    </h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm">
                        @csrf

                        <!-- Payment Method Options -->
                        <div class="row">
                            @php
                                $paymentMethods = config('showcase.payment_methods');
                                $enabledMethods = collect($paymentMethods)->filter(function($method) {
                                    return $method['enabled'];
                                });
                                $showComingSoon = config('showcase.demo.show_coming_soon_methods', true);
                            @endphp

                            @foreach($paymentMethods as $key => $method)
                                @if($method['enabled'] || ($showComingSoon && !$method['enabled']))
                                    <div class="col-md-6 mb-3">
                                        <input type="radio" class="btn-check" name="payment_method" id="payment_{{ $key }}" value="{{ $key }}"
                                               {{ $selectedPaymentMethod === $key ? 'checked' : '' }}
                                               {{ !$method['enabled'] ? 'disabled' : '' }}>
                                        <label class="btn {{ $method['enabled'] ? 'btn-outline-secondary' : 'btn-outline-secondary opacity-50' }} w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                               for="payment_{{ $key }}" style="min-height: 120px; {{ !$method['enabled'] ? 'cursor: not-allowed;' : '' }}">
                                            <i class="{{ $method['icon'] }} fa-2x mb-2" style="color: {{ $method['enabled'] ? ($key === 'card' ? '#328a75' : 'inherit') : '#6c757d' }};"></i>
                                            <strong>{{ $method['name'] }}</strong>
                                            <small class="text-muted mt-1">{{ $method['description'] }}</small>

                                            @if($method['enabled'] && $method['demo_functional'])
                                                <span class="badge bg-success mt-2">Functional</span>
                                            @elseif($method['enabled'] && !$method['demo_functional'])
                                                <span class="badge bg-warning mt-2">Demo Only</span>
                                            @else
                                                <span class="badge bg-secondary mt-2">Coming Soon</span>
                                            @endif

                                            @if($key === 'card' && $method['enabled'])
                                                <div class="mt-2">
                                                    <i class="fab fa-cc-visa me-1" style="font-size: 1.5rem; color: #1a1f71;"></i>
                                                    <i class="fab fa-cc-mastercard me-1" style="font-size: 1.5rem; color: #eb001b;"></i>
                                                    <i class="fab fa-cc-amex" style="font-size: 1.5rem; color: #006fcf;"></i>
                                                </div>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if($showComingSoon)
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>
                                    <strong>Development Status:</strong> Only enabled payment methods are functional.
                                    Disabled methods show the SDK integration structure for future implementation.
                                </small>
                            </div>
                        @endif

                        <!-- Payment Method Details -->
                        <div id="payment-details" class="mt-4" style="display: none;">
                            <!-- Card Details Info (No form - handled by Gateway) -->
                            <div id="card-details" class="payment-details" style="display: none;">
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <strong>Credit/Debit Card Selected:</strong> You'll enter your card details securely on the next step via our payment gateway.
                                </div>
                            </div>

                            <!-- Digital Wallet Info -->
                            <div id="wallet-details" class="payment-details" style="display: none;">
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    <strong>Digital Wallet Selected:</strong> You'll be prompted to authenticate using your device when you proceed.
                                </div>
                            </div>

                            <!-- PayPal Info -->
                            <div id="paypal-details" class="payment-details" style="display: none;">
                                <div class="alert alert-info" role="alert">
                                    <i class="fab fa-paypal me-2"></i>
                                    <strong>PayPal Payment:</strong> You'll be redirected to PayPal to complete your payment securely.
                                </div>
                            </div>

                            <!-- Klarna Info -->
                            <div id="klarna-details" class="payment-details" style="display: none;">
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <strong>Pay with Klarna:</strong> Split your payment into 4 interest-free installments.
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4" role="alert">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Secure Checkout:</strong> Your payment information is encrypted and secure. This is a demo environment.
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Info Summary -->
            <div class="card border-0 shadow-sm">
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
                            <span class="text-muted">{{ $customerData['email'] }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted">{{ $customerData['phone'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">Order Summary</h6>
                </div>
                <div class="card-body">
                    <!-- Cart Items -->
                    @foreach($cartItems as $item)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            @if($item['product']->image)
                            <img src="{{ $item['product']->image }}"
                                 alt="{{ $item['product']->name }}"
                                 class="me-3"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                            <div class="me-3 d-flex align-items-center justify-content-center bg-light text-muted"
                                 style="width: 50px; height: 50px; border-radius: 8px;">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                            <div>
                                <h6 class="mb-0" style="font-size: 0.9rem;">{{ $item['product']->name }}</h6>
                                <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                            </div>
                        </div>
                        <span class="fw-bold">€{{ number_format($item['product']->price * $item['quantity'], 2) }}</span>
                    </div>
                    @endforeach

                    <hr>

                    <!-- Order Total -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Subtotal:</span>
                        <span>€{{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Shipping:</span>
                        <span class="text-success">Free</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Tax:</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Total:</h6>
                        <h5 class="mb-0" style="color: #328a75;">€{{ number_format($cartTotal, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-3">
                <button type="button" class="btn btn-lg w-100 text-white mb-3"
                        style="background-color: #328a75; border-color: #328a75;"
                        id="continueBtn" disabled>
                    <span id="btnText">Review Order</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>
                <a href="{{ route('shop.checkout.step', ['step' => 'customer']) }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customer Info
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const continueBtn = document.getElementById('continueBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const paymentDetails = document.getElementById('payment-details');

    // Payment method radio buttons
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

    // Initialize
    updatePaymentDetails();
    updateContinueButton();

    // Listen for payment method changes
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updatePaymentDetails();
            updateContinueButton();
        });
    });

    function updatePaymentDetails() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');

        // Hide all details
        document.querySelectorAll('.payment-details').forEach(detail => {
            detail.style.display = 'none';
        });

        if (selectedMethod) {
            paymentDetails.style.display = 'block';

            switch (selectedMethod.value) {
                case 'card':
                    document.getElementById('card-details').style.display = 'block';
                    break;
                case 'apple_pay':
                case 'google_pay':
                    document.getElementById('wallet-details').style.display = 'block';
                    break;
                case 'paypal':
                    document.getElementById('paypal-details').style.display = 'block';
                    break;
                case 'klarna':
                    document.getElementById('klarna-details').style.display = 'block';
                    break;
            }
        } else {
            paymentDetails.style.display = 'none';
        }
    }

    function updateContinueButton() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked:not(:disabled)');
        continueBtn.disabled = !selectedMethod;
    }

    // Handle clicks on disabled payment methods
    document.querySelectorAll('input[name="payment_method"]:disabled').forEach(function(input) {
        const label = document.querySelector(`label[for="${input.id}"]`);
        if (label) {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                const methodName = label.querySelector('strong').textContent;
                Swal.fire({
                    icon: 'info',
                    title: `${methodName} Coming Soon`,
                    text: 'This payment method is not yet available in the demo. Only Credit/Debit Card payments are currently functional.',
                    confirmButtonColor: '#328a75'
                });
            });
        }
    });

    // Card details are handled by the Gateway - no local formatting needed

    continueBtn.addEventListener('click', function() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');

        if (!selectedMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Method Required',
                text: 'Please select a payment method to continue.'
            });
            return;
        }

        // Show loading state
        continueBtn.disabled = true;
        btnText.textContent = 'Processing...';
        btnSpinner.classList.remove('d-none');

        // Submit form
        const formData = new FormData(form);

        fetch('{{ route("shop.checkout.process.payment") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect_to_gateway && data.payment_url) {
                    // Redirect to Gateway payment page
                    window.location.href = data.payment_url;
                } else {
                    // Normal redirect (fallback)
                    window.location.href = data.redirect;
                }
            } else {
                throw new Error(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An error occurred while processing your payment method. Please try again.'
            });
        })
        .finally(() => {
            // Reset button state
            continueBtn.disabled = false;
            btnText.textContent = 'Review Order';
            btnSpinner.classList.add('d-none');
            updateContinueButton(); // Recheck if method is selected
        });
    });
});
</script>

<style>
/* Selected payment method styling */
.btn-check:checked + .btn-outline-secondary {
    background-color: #328a75 !important;
    border-color: #328a75 !important;
    color: white !important;
}

.btn-check:checked + .btn-outline-secondary strong {
    color: white !important;
}

.btn-check:checked + .btn-outline-secondary small {
    color: white !important;
}

.btn-check:checked + .btn-outline-secondary i {
    color: white !important;
}

.btn-check:checked + .btn-outline-secondary .badge.bg-success {
    background-color: black !important;
    color: white !important;
}

.btn-check:checked + .btn-outline-secondary .fab {
    color: white !important;
}
</style>
@endsection
