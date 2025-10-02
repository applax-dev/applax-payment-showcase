@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5" style="background: linear-gradient(135deg, #328a75 0%, #4b6ba2 100%); padding-top: 50px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Applax Payment Gateway
                    <span class="d-block h3 fw-normal text-white-75 mt-2">Complete SDK Showcase & Integration Demo</span>
                </h1>
                <p class="lead mb-4">
                    Experience the full power of the <code class="text-warning">applax-dev/gate-sdk</code> package with our
                    comprehensive showcase featuring all payment methods, real-time webhooks, and complete order management.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('shop.products.index') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-shop me-2"></i>Try Demo Shop
                    </a>
                    <a href="{{ route('sdk.showcase') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-code-slash me-2"></i>Explore SDK
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-gear me-2"></i>Admin Panel
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white bg-opacity-10 p-4 rounded-3">
                    <i class="bi bi-shield-check display-1 text-warning mb-3"></i>
                    <h4>Enterprise Security</h4>
                    <p class="mb-0">PSR-compatible, type-safe, with comprehensive error handling</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Features -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col">
                <h2 class="display-5 fw-bold text-primary mb-3">Complete Payment Solution</h2>
                <p class="lead text-muted">
                    Everything you need to integrate payment processing with the Applax Gate SDK
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Payment Methods -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 payment-method-card">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-credit-card display-6 text-white"></i>
                        </div>
                        <h4 class="card-title text-primary">Multiple Payment Methods</h4>
                        <p class="card-text">
                            Cards, Apple Pay, Google Pay, PayPal, Klarna, Bank Transfers, and more.
                        </p>
                        <ul class="list-unstyled small text-muted mb-3">
                            <li><i class="bi bi-check2 text-success me-1"></i>Credit/Debit Cards</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Digital Wallets</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Alternative Payment Methods</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>MOTO Payments</li>
                        </ul>
                        <a href="{{ route('payment.cards') }}" class="btn btn-outline-primary">Explore Methods</a>
                    </div>
                </div>
            </div>

            <!-- SDK Integration -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 payment-method-card">
                    <div class="card-body text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-code-slash display-6 text-success"></i>
                        </div>
                        <h4 class="card-title text-success">Complete SDK Coverage</h4>
                        <p class="card-text">
                            Every single method of the gate-sdk is demonstrated with live examples.
                        </p>
                        <ul class="list-unstyled small text-muted mb-3">
                            <li><i class="bi bi-check2 text-success me-1"></i>Products Management</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Customer Management</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Order Processing</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Webhook Handling</li>
                        </ul>
                        <a href="{{ route('sdk.showcase') }}" class="btn btn-outline-success">View SDK Docs</a>
                    </div>
                </div>
            </div>

            <!-- Admin Dashboard -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 payment-method-card">
                    <div class="card-body text-center">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-graph-up display-6 text-info"></i>
                        </div>
                        <h4 class="card-title text-info">Real-time Management</h4>
                        <p class="card-text">
                            Complete order management with live webhook updates and analytics.
                        </p>
                        <ul class="list-unstyled small text-muted mb-3">
                            <li><i class="bi bi-check2 text-success me-1"></i>Order Management</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Payment Processing</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Live Webhook Logs</li>
                            <li><i class="bi bi-check2 text-success me-1"></i>Analytics Dashboard</li>
                        </ul>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">Open Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Live Demo -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h3 class="fw-bold text-primary mb-4">
                    <i class="bi bi-play-circle me-2"></i>Interactive Demo
                </h3>
                <p class="lead mb-4">
                    Test the complete payment flow with our sample e-commerce experience.
                    All payments use sandbox mode with test data.
                </p>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="text-white fw-bold">1</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Browse Products</h6>
                                <p class="text-muted small mb-0">Sample product catalog</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="text-white fw-bold">2</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Add to Cart</h6>
                                <p class="text-muted small mb-0">Shopping cart functionality</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="text-white fw-bold">3</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Choose Payment</h6>
                                <p class="text-muted small mb-0">Multiple payment options</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="text-white fw-bold">4</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Complete Order</h6>
                                <p class="text-muted small mb-0">Real-time processing</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-cart-plus me-2"></i>Start Shopping Demo
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <h3 class="fw-bold text-primary mb-4">
                    <i class="bi bi-code-square me-2"></i>SDK Integration Example
                </h3>
                <div class="code-block">
<pre><span class="comment">// Initialize the SDK</span>
<span class="keyword">use</span> ApplaxDev\GateSDK\GateSDK;

<span class="keyword">$sdk</span> = <span class="keyword">new</span> <span class="function">GateSDK</span>(
    <span class="string">apiKey:</span> <span class="string">'your-api-key'</span>,
    <span class="string">sandbox:</span> <span class="keyword">true</span>
);

<span class="comment">// Create an order</span>
<span class="keyword">$order</span> = <span class="keyword">$sdk</span>-><span class="function">createOrder</span>([
    <span class="string">'client'</span> => [
        <span class="string">'email'</span> => <span class="string">'customer@example.com'</span>,
        <span class="string">'first_name'</span> => <span class="string">'John'</span>,
        <span class="string">'last_name'</span> => <span class="string">'Doe'</span>
    ],
    <span class="string">'products'</span> => [
        [
            <span class="string">'title'</span> => <span class="string">'Premium Service'</span>,
            <span class="string">'price'</span> => <span class="string">29.99</span>,
            <span class="string">'quantity'</span> => <span class="string">1</span>
        ]
    ],
    <span class="string">'currency'</span> => <span class="string">'EUR'</span>
]);

<span class="comment">// Process payment</span>
<span class="keyword">$result</span> = <span class="keyword">$sdk</span>-><span class="function">executeCardPayment</span>(<span class="keyword">$order</span>[<span class="string">'api_do_url'</span>], [
    <span class="string">'cardholder_name'</span> => <span class="string">'John Doe'</span>,
    <span class="string">'card_number'</span> => <span class="string">'4111111111111111'</span>,
    <span class="string">'cvv'</span> => <span class="string">'123'</span>,
    <span class="string">'exp_month'</span> => <span class="string">'12'</span>,
    <span class="string">'exp_year'</span> => <span class="string">'2025'</span>
]);</pre>
                </div>
                <div class="mt-3">
                    <button class="btn btn-outline-primary" onclick="copyToClipboard(this)" data-bs-toggle="tooltip" title="Copy code to clipboard">
                        <i class="bi bi-clipboard me-1"></i>Copy Code
                    </button>
                    <a href="{{ route('sdk.showcase') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-book me-1"></i>Full Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <div class="display-4 fw-bold text-primary mb-2">11</div>
                        <h6 class="text-uppercase text-muted mb-0">Payment Methods</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <div class="display-4 fw-bold text-success mb-2">50+</div>
                        <h6 class="text-uppercase text-muted mb-0">SDK Methods</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <div class="display-4 fw-bold text-info mb-2">100%</div>
                        <h6 class="text-uppercase text-muted mb-0">Feature Coverage</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <div class="display-4 fw-bold text-warning mb-2">24/7</div>
                        <h6 class="text-uppercase text-muted mb-0">Demo Available</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
<script>
function copyToClipboard(button) {
    const codeBlock = button.closest('div').previousElementSibling.querySelector('pre');
    const textArea = document.createElement('textarea');
    textArea.value = codeBlock.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);

    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check text-success me-1"></i>Copied!';

    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
@endpush
