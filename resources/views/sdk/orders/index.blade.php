@extends('layouts.app')

@section('title', 'Orders API - SDK Showcase')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1" style="color: #3b4151;">
                        <i class="bi bi-receipt me-2"></i>Orders API Showcase
                    </h1>
                    <p class="text-muted mb-0">
                        Live demonstration of Applax Gate SDK Order management and payment processing methods
                    </p>
                </div>
                <div class="text-end">
                    <div class="d-flex gap-2">
                        <a href="https://docs.appla-x.com/" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-book me-1"></i>Documentation
                        </a>
                        <a href="https://gate.appla-x.com/" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-shield-check me-1"></i>Gateway Portal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sdk.showcase') }}">SDK Showcase</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Orders API</li>
                </ol>
            </nav>

            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Live SDK Integration:</strong> This page demonstrates actual calls to the Applax Gateway for order management,
                        payment capture, refunds, and cancellations using our GateSDKService wrapper.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- SDK Methods Panel -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-code-square me-2"></i>SDK Methods
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#create-order" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>createOrder()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Create a new order with client and products</small>
                        </a>

                        <a href="#get-orders" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getOrders()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Retrieve orders list with filters</small>
                        </a>

                        <a href="#get-order" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getOrder()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Get single order details by ID</small>
                        </a>

                        <a href="#capture-payment" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">POST</span>
                                <code>capturePayment()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Capture pending payment</small>
                        </a>

                        <a href="#refund-payment" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">POST</span>
                                <code>refundPayment()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Process payment refund</small>
                        </a>

                        <a href="#cancel-order" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">POST</span>
                                <code>cancelOrder()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Cancel order</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SDK Demonstrations -->
        <div class="col-lg-8">
            <!-- Create Order -->
            <div id="create-order" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-success me-2">POST</span>
                        Create Order
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a new order with client information and products in the Applax Gateway.</p>

                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Testing Payment Capture Workflow
                        </h6>
                        <p class="mb-2">Orders created here have <code>skip_capture: true</code> to enable manual capture testing. After creating an order:</p>
                        <ol class="mb-2">
                            <li><strong>Copy the Payment Link:</strong> From the response, copy either:
                                <ul class="mt-1 mb-1">
                                    <li><code>full_page_checkout</code> - Complete payment page</li>
                                    <li><code>iframe_checkout</code> - Embedded payment form</li>
                                </ul>
                            </li>
                            <li><strong>Make Payment:</strong> Open the link in a new tab and complete the payment using test card: <code>4111 1111 1111 1111</code></li>
                            <li><strong>Verify Status:</strong> Use "Get Single Order" to confirm order status shows "authorized" (not "captured")</li>
                            <li><strong>Capture Payment:</strong> Use the "Capture Payment" section below with the order ID</li>
                        </ol>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tip:</strong> Orders with <code>skip_capture: false</code> auto-capture when paid and cannot be manually captured later.
                        </small>
                    </div>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('create-order-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="create-order-code" class="language-php">$orderData = [
    'client' => [
        'email' => 'customer@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '+1234567890',
    ],
    'products' => [
        [
            'title' => 'Premium Headphones',
            'price' => 199.99,
            'quantity' => 1,
        ]
    ],
    'currency' => 'EUR',
    'brand' => config('services.gate.brand_id'),
];

$result = $gateSDKService->createOrder($orderData);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="create-order-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Email *</label>
                                    <input type="email" class="form-control" name="client_email" required placeholder="customer@example.com">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="client_first_name" required placeholder="John">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="client_last_name" required placeholder="Doe">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Client Phone</label>
                                    <input type="text" class="form-control" name="client_phone" placeholder="+1234567890">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Product Title *</label>
                                    <input type="text" class="form-control" name="product_title" required placeholder="Premium Headphones">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" class="form-control" name="product_price" step="0.01" min="0.01" required placeholder="199.99">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="quantity" min="1" placeholder="1" value="1">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Currency *</label>
                                    <select class="form-control" name="currency" required>
                                        <option value="EUR">EUR</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>Create Order in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="create-order-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Orders -->
            <div id="get-orders" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Orders
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve orders from the Gateway with optional filters.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-orders-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-orders-code" class="language-php">$filters = [
    'limit' => 10,
    'offset' => 0,
    'status' => 'paid' // Optional: filter by status
];

$result = $gateSDKService->getOrders($filters);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="get-orders-form">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Limit</label>
                                    <input type="number" class="form-control" name="limit" min="1" max="50" placeholder="10" value="10">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Offset</label>
                                    <input type="number" class="form-control" name="offset" min="0" placeholder="0" value="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select class="form-control" name="status">
                                        <option value="">All statuses</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Orders from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-orders-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Single Order -->
            <div id="get-order" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Single Order
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve a specific order by its Gateway ID.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-order-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-order-code" class="language-php">$orderId = '550e8400-e29b-41d4-a716-446655440000';

$result = $gateSDKService->getOrder($orderId);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="get-order-form">
                            <div class="mb-3">
                                <label class="form-label">Order ID *</label>
                                <input type="text" class="form-control" name="order_id" required placeholder="Enter Gateway Order ID">
                                <small class="form-text text-muted">Use the Order ID from a previously created order</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i>Get Order Details
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-order-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Capture Payment -->
            <div id="capture-payment" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-info me-2">POST</span>
                        Capture Payment
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Capture a pending payment for an order.</p>

                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Prerequisites for Payment Capture
                        </h6>
                        <p class="mb-2">Before you can capture a payment, ensure:</p>
                        <ul class="mb-2">
                            <li>The order was created with <code>skip_capture: true</code></li>
                            <li>Customer has completed payment using the payment link</li>
                            <li>Order status shows as "authorized" (not "captured" or "issued")</li>
                            <li>The order's <code>permitted_actions</code> array includes "capture"</li>
                        </ul>
                        <p class="mb-0">
                            <strong>⚠️ Common Error:</strong> "Invalid input" means the order cannot be captured -
                            either it wasn't paid yet, was already captured, or was created with auto-capture enabled.
                        </p>
                    </div>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('capture-payment-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="capture-payment-code" class="language-php">$orderId = '550e8400-e29b-41d4-a716-446655440000';
$amount = 199.99; // Optional: partial capture amount

$result = $gateSDKService->capturePayment($orderId, $amount);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="capture-payment-form">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Order ID *</label>
                                    <input type="text" class="form-control" name="order_id" required placeholder="Enter Gateway Order ID">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Amount (Optional)</label>
                                    <input type="number" class="form-control" name="amount" step="0.01" min="0.01" placeholder="Full amount">
                                    <small class="form-text text-muted">Leave empty for full capture</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-credit-card me-1"></i>Capture Payment
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="capture-payment-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Refund Payment -->
            <div id="refund-payment" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-warning me-2">POST</span>
                        Refund Payment
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Process a refund for a completed payment.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('refund-payment-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="refund-payment-code" class="language-php">$orderId = '550e8400-e29b-41d4-a716-446655440000';
$amount = 99.99;
$reason = 'Customer request';

$result = $gateSDKService->refundPayment($orderId, $amount, $reason);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will process a real refund in the Gateway system.
                        </div>
                        <form id="refund-payment-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Order ID *</label>
                                    <input type="text" class="form-control" name="order_id" required placeholder="Enter Gateway Order ID">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Refund Amount *</label>
                                    <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required placeholder="99.99">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Refund Reason</label>
                                <input type="text" class="form-control" name="reason" placeholder="Customer request" maxlength="255">
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Process Refund
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="refund-payment-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Cancel Order -->
            <div id="cancel-order" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-danger me-2">POST</span>
                        Cancel Order
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Cancel an order in the Gateway system.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('cancel-order-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="cancel-order-code" class="language-php">$orderId = '550e8400-e29b-41d4-a716-446655440000';

$result = $gateSDKService->cancelOrder($orderId);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will cancel the order in the Gateway. This action cannot be undone.
                        </div>
                        <form id="cancel-order-form">
                            <div class="mb-3">
                                <label class="form-label">Order ID *</label>
                                <input type="text" class="form-control" name="order_id" required placeholder="Enter Gateway Order ID">
                                <small class="form-text text-muted">Order will be cancelled in the Gateway</small>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i>Cancel Order in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="cancel-order-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="mt-2">Calling Gateway API...</div>
</div>

<script>
// Form handlers
document.getElementById('create-order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/orders/create', new FormData(this), 'create-order-response');
});

document.getElementById('get-orders-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/orders/get', new FormData(this), 'get-orders-response');
});

document.getElementById('get-order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/orders/get-single', new FormData(this), 'get-order-response');
});

document.getElementById('capture-payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/orders/capture', new FormData(this), 'capture-payment-response');
});

document.getElementById('refund-payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to process this refund? This action cannot be undone.')) {
        makeApiCall('/sdk/orders/refund', new FormData(this), 'refund-payment-response');
    }
});

document.getElementById('cancel-order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        makeApiCall('/sdk/orders/cancel', new FormData(this), 'cancel-order-response');
    }
});

// API call function
function makeApiCall(url, formData, responseContainerId) {
    const loadingOverlay = document.getElementById('loading-overlay');
    const responseContainer = document.getElementById(responseContainerId);

    loadingOverlay.style.display = 'flex';
    responseContainer.style.display = 'none';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingOverlay.style.display = 'none';

        const responseContent = responseContainer.querySelector('.response-content');
        responseContent.textContent = JSON.stringify(data, null, 2);

        if (data.success) {
            responseContainer.className = 'response-container success-response';
        } else {
            responseContainer.className = 'response-container error-response';
        }

        responseContainer.style.display = 'block';
        responseContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    })
    .catch(error => {
        loadingOverlay.style.display = 'none';
        console.error('Error:', error);

        const responseContent = responseContainer.querySelector('.response-content');
        responseContent.textContent = JSON.stringify({
            success: false,
            message: 'Network error occurred',
            error: error.message
        }, null, 2);

        responseContainer.className = 'response-container error-response';
        responseContainer.style.display = 'block';
    });
}

// Copy to clipboard function
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    navigator.clipboard.writeText(element.textContent).then(() => {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}

// Smooth scrolling for navigation
document.querySelectorAll('.method-nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        targetElement.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    font-weight: 500;
    color: #3b4151;
}

.response-container {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.response-container.success-response {
    background-color: #d1edff;
    border-color: #0d6efd;
}

.response-container.error-response {
    background-color: #f8d7da;
    border-color: #dc3545;
}

.response-container pre {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin: 0;
    max-height: 400px;
    overflow-y: auto;
}

.response-container code {
    font-size: 0.85rem;
    color: #3b4151;
}

pre code {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    display: block;
    overflow-x: auto;
    font-size: 0.875rem;
    line-height: 1.5;
}

.method-nav-link {
    text-decoration: none !important;
}

.method-nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
}

.sticky-top {
    position: sticky;
}
</style>
@endsection