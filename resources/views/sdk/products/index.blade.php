@extends('layouts.app')

@section('title', 'Products API - SDK Showcase')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1" style="color: #3b4151;">
                        <i class="bi bi-box-seam me-2"></i>Products API Showcase
                    </h1>
                    <p class="text-muted mb-0">
                        Live demonstration of Applax Gate SDK Product management methods
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
                    <li class="breadcrumb-item active" aria-current="page">Products API</li>
                </ol>
            </nav>

            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Live SDK Integration:</strong> This page demonstrates actual calls to the Applax Gateway using our GateSDKService wrapper.
                        All operations create, retrieve, update, and delete products in the real Gateway system.
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
                        <a href="#create-product" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>createProduct()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Create a new product in Gateway</small>
                        </a>

                        <a href="#get-products" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getProducts()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Retrieve products list with filters</small>
                        </a>

                        <a href="#get-product" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getProduct()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Get single product by ID</small>
                        </a>

                        <a href="#update-product" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">PUT</span>
                                <code>updateProduct()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Update existing product</small>
                        </a>

                        <a href="#delete-product" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">DEL</span>
                                <code>deleteProduct()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Delete product from Gateway</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SDK Demonstrations -->
        <div class="col-lg-8">
            <!-- Create Product -->
            <div id="create-product" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-success me-2">POST</span>
                        Create Product
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a new product in the Applax Gateway system.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('create-product-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="create-product-code" class="language-php">$productData = [
    'name' => 'Premium Headphones',
    'amount' => 19999, // $199.99 in cents
    'currency' => 'EUR',
    'type' => 'product'
];

$result = $gateSDKService->createProduct($productData);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="create-product-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" name="name" required placeholder="e.g. Premium Headphones">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" class="form-control" name="price" step="0.01" min="0.01" required placeholder="199.99">
                                </div>
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
                                <i class="bi bi-play-circle me-1"></i>Create Product in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="create-product-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Products -->
            <div id="get-products" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Products
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve products from the Gateway with optional filters.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-products-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-products-code" class="language-php">$filters = [
    'limit' => 10,
    'offset' => 0
];

$result = $gateSDKService->getProducts($filters);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="get-products-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cursor (Optional)</label>
                                    <input type="text" class="form-control" name="cursor" placeholder="Enter cursor for pagination">
                                    <small class="form-text text-muted">Use cursor from previous response for pagination</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Search Query (Optional)</label>
                                    <input type="text" class="form-control" name="q" placeholder="Search products">
                                    <small class="form-text text-muted">Search by product name or ID</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Filter Title (Optional)</label>
                                    <input type="text" class="form-control" name="filter_title" placeholder="Filter by title">
                                    <small class="form-text text-muted">Filter products by title</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Filter Price (Optional)</label>
                                    <input type="number" class="form-control" name="filter_price" step="0.01" min="0" placeholder="Filter by price">
                                    <small class="form-text text-muted">Filter products by price</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Products from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-products-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Single Product -->
            <div id="get-product" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Single Product
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve a specific product by its Gateway ID.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-product-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-product-code" class="language-php">$productId = '550e8400-e29b-41d4-a716-446655440000';

$result = $gateSDKService->getProduct($productId);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="get-product-form">
                            <div class="mb-3">
                                <label class="form-label">Product ID *</label>
                                <input type="text" class="form-control" name="product_id" required placeholder="Enter Gateway Product ID">
                                <small class="form-text text-muted">Use the Product ID from a previously created product</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i>Get Product Details
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-product-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Update Product -->
            <div id="update-product" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-warning me-2">PUT</span>
                        Update Product
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Update an existing product in the Gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('update-product-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="update-product-code" class="language-php">$productId = '550e8400-e29b-41d4-a716-446655440000';
$updateData = [
    'name' => 'Premium Headphones Pro',
    'amount' => 24999, // $249.99 in cents
    'currency' => 'EUR',
    'type' => 'product'
];

$result = $gateSDKService->updateProduct($productId, $updateData);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <form id="update-product-form">
                            <div class="mb-3">
                                <label class="form-label">Product ID *</label>
                                <input type="text" class="form-control" name="product_id" required placeholder="Enter Gateway Product ID">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" name="name" required placeholder="e.g. Premium Headphones Pro">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" class="form-control" name="price" step="0.01" min="0.01" required placeholder="249.99">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Currency *</label>
                                    <select class="form-control" name="currency" required>
                                        <option value="EUR">EUR</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-1"></i>Update Product in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="update-product-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Delete Product -->
            <div id="delete-product" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-danger me-2">DEL</span>
                        Delete Product
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Delete a product from the Gateway system.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('delete-product-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="delete-product-code" class="language-php">$productId = '550e8400-e29b-41d4-a716-446655440000';

$result = $gateSDKService->deleteProduct($productId);</code></pre>
                    </div>

                    <!-- Interactive Form -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-3">Try It Live</h6>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will permanently delete the product from the Gateway. This action cannot be undone.
                        </div>
                        <form id="delete-product-form">
                            <div class="mb-3">
                                <label class="form-label">Product ID *</label>
                                <input type="text" class="form-control" name="product_id" required placeholder="Enter Gateway Product ID">
                                <small class="form-text text-muted">Product will be permanently deleted from Gateway</small>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete Product from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="delete-product-response" class="response-container" style="display: none;">
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
document.getElementById('create-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/products/create', new FormData(this), 'create-product-response');
});

document.getElementById('get-products-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/products/get', new FormData(this), 'get-products-response');
});

document.getElementById('get-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/products/get-single', new FormData(this), 'get-product-response');
});

document.getElementById('update-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    makeApiCall('/sdk/products/update', new FormData(this), 'update-product-response');
});

document.getElementById('delete-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        makeApiCall('/sdk/products/delete', new FormData(this), 'delete-product-response');
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
        // Show success feedback
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
    max-height: 300px;
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