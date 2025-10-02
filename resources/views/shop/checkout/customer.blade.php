@extends('layouts.app')

@section('title', 'Checkout - Customer Information')

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
                                     style="width: 40px; height: 40px; background-color: #328a75; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <small class="fw-bold" style="color: #328a75;">Customer Info</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #e9ecef; color: #6c757d;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <small class="text-muted">Payment Method</small>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="customerForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="first_name"
                                           name="first_name"
                                           value="{{ $customerData['first_name'] ?? '' }}"
                                           placeholder="Enter your first name"
                                           required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ $customerData['last_name'] ?? '' }}"
                                           placeholder="Enter your last name"
                                           required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="{{ $customerData['email'] ?? '' }}"
                                   placeholder="Enter your email address"
                                   required>
                            <div class="invalid-feedback"></div>
                            <div class="form-text">We'll use this to send you order updates.</div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel"
                                   class="form-control"
                                   id="phone"
                                   name="phone"
                                   value="{{ $customerData['phone'] ?? '' }}"
                                   placeholder="Enter your phone number"
                                   required>
                            <input type="hidden" id="phone_full" name="phone_full" value="">
                            <input type="hidden" id="phone_country_code" name="phone_country_code" value="">
                            <div class="invalid-feedback"></div>
                            <div class="form-text">We'll only use this for order-related communications.</div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Demo Note:</strong> This is a demonstration checkout. No real payment will be processed.
                        </div>
                    </form>
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
                        id="continueBtn">
                    <span id="btnText">Continue to Payment</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>
                <a href="{{ route('shop.cart.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Back to Cart
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customerForm');
    const continueBtn = document.getElementById('continueBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    // Initialize international telephone input
    const phoneInput = document.querySelector("#phone");
    const phoneFullInput = document.querySelector("#phone_full");
    const phoneCountryCodeInput = document.querySelector("#phone_country_code");

    const iti = window.intlTelInput(phoneInput, {
        nationalMode: false,
        preferredCountries: ['ro', 'us', 'gb'],
        initialCountry: "ro",
        separateDialCode: true,
        autoPlaceholder: "aggressive",
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
    });

    // Update hidden fields when phone changes
    function updatePhoneData() {
        if (iti.isValidNumber()) {
            const fullNumber = iti.getNumber();
            const countryCode = iti.getSelectedCountryData().dialCode;

            phoneFullInput.value = fullNumber;
            phoneCountryCodeInput.value = countryCode;

            // Clear any validation errors
            phoneInput.classList.remove('is-invalid');
        }
    }

    phoneInput.addEventListener('input', updatePhoneData);
    phoneInput.addEventListener('countrychange', updatePhoneData);

    continueBtn.addEventListener('click', function() {
        // Clear previous validation states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Update phone data before validation
        updatePhoneData();

        // Basic validation
        let isValid = true;
        const requiredFields = ['first_name', 'last_name', 'email'];

        requiredFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                input.nextElementSibling.textContent = 'This field is required.';
                isValid = false;
            }
        });

        // Phone validation using intl-tel-input
        if (!phoneInput.value.trim()) {
            phoneInput.classList.add('is-invalid');
            phoneInput.nextElementSibling.nextElementSibling.nextElementSibling.textContent = 'Phone number is required.';
            isValid = false;
        } else if (!iti.isValidNumber()) {
            phoneInput.classList.add('is-invalid');
            phoneInput.nextElementSibling.nextElementSibling.nextElementSibling.textContent = 'Please enter a valid phone number.';
            isValid = false;
        }

        // Email validation
        const emailInput = form.querySelector('[name="email"]');
        if (emailInput.value && !isValidEmail(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            emailInput.nextElementSibling.textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Show loading state
        continueBtn.disabled = true;
        btnText.textContent = 'Processing...';
        btnSpinner.classList.remove('d-none');

        // Submit form
        const formData = new FormData(form);

        fetch('{{ route("shop.checkout.process.customer") }}', {
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
                window.location.href = data.redirect;
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            input.nextElementSibling.textContent = data.errors[field][0];
                        }
                    });
                }
                throw new Error(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An error occurred while processing your information. Please try again.'
            });
        })
        .finally(() => {
            // Reset button state
            continueBtn.disabled = false;
            btnText.textContent = 'Continue to Payment';
            btnSpinner.classList.add('d-none');
        });
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>
@endsection
