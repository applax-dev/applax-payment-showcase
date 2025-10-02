@extends('layouts.app')

@section('title', 'SDK Showcase - Applax Payment Gateway')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5 bg-gradient-primary text-white rounded-3 mb-5" style="background: linear-gradient(135deg, #328a75 0%, #4b6ba2 100%);">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <i class="fas fa-code fa-4x mb-4 opacity-75"></i>
                            <h1 class="display-4 fw-bold mb-3">Applax Gate SDK Showcase</h1>
                            <p class="lead mb-4">
                                Explore our comprehensive payment gateway SDK with interactive demonstrations of all available APIs.
                                Test real Gateway integrations for Products, Orders, Clients, and Webhooks management.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="https://docs.appla-x.com/" target="_blank" class="btn btn-light btn-lg">
                                    <i class="fas fa-book me-2"></i>API Documentation
                                </a>
                                <a href="https://gate.appla-x.com/" target="_blank" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-external-link-alt me-2"></i>Gateway Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SDK API Cards -->
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="h3 text-center mb-5">Interactive SDK Demonstrations</h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Products API -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-wrapper p-3 rounded-3 me-3" style="background-color: {{ config('app.brand_primary_color') }}1A;">
                            <i class="fas fa-box fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="card-title h4 mb-3 text-primary">Products API</h3>
                            <p class="card-text text-muted mb-4">
                                Manage products in your Gateway catalog. Create, retrieve, update, and delete products
                                with real-time API integration. Test CRUD operations with live Gateway responses.
                            </p>
                            <div class="mb-3">
                                <span class="badge bg-success me-2">CREATE</span>
                                <span class="badge bg-primary me-2">READ</span>
                                <span class="badge bg-warning me-2">UPDATE</span>
                                <span class="badge bg-danger">DELETE</span>
                            </div>
                            <a href="{{ route('sdk.products') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-play me-2"></i>Explore Products API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders API -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-wrapper p-3 rounded-3 me-3" style="background-color: {{ config('app.brand_primary_color') }}1A;">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="card-title h4 mb-3 text-primary">Orders API</h3>
                            <p class="card-text text-muted mb-4">
                                Complete order lifecycle management. Create orders with customer data, manage payment
                                captures, process refunds, and handle order cancellations through the Gateway.
                            </p>
                            <div class="mb-3">
                                <span class="badge bg-success me-2">CREATE</span>
                                <span class="badge bg-warning me-2">CAPTURE</span>
                                <span class="badge bg-secondary me-2">REFUND</span>
                                <span class="badge bg-danger">CANCEL</span>
                            </div>
                            <a href="{{ route('sdk.orders') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-play me-2"></i>Explore Orders API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients API -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-wrapper p-3 rounded-3 me-3" style="background-color: {{ config('app.brand_primary_color') }}1A;">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="card-title h4 mb-3 text-primary">Clients API</h3>
                            <p class="card-text text-muted mb-4">
                                Customer profile and data management. Create client profiles, manage customer information,
                                update contact details, and maintain customer database through the Gateway.
                            </p>
                            <div class="mb-3">
                                <span class="badge bg-success me-2">CREATE</span>
                                <span class="badge bg-warning me-2">UPDATE</span>
                                <span class="badge bg-info me-2">PATCH</span>
                                <span class="badge bg-danger">DELETE</span>
                            </div>
                            <a href="{{ route('sdk.clients') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-play me-2"></i>Explore Clients API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Webhooks API -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-wrapper p-3 rounded-3 me-3" style="background-color: {{ config('app.brand_primary_color') }}1A;">
                            <i class="fas fa-link fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="card-title h4 mb-3 text-primary">Webhooks API</h3>
                            <p class="card-text text-muted mb-4">
                                Event notification system management. Create webhook endpoints, configure event
                                subscriptions, test webhook deliveries, and manage notification preferences.
                            </p>
                            <div class="mb-3">
                                <span class="badge bg-success me-2">CREATE</span>
                                <span class="badge bg-primary me-2">CONFIGURE</span>
                                <span class="badge bg-warning me-2">TEST</span>
                                <span class="badge bg-danger">DELETE</span>
                            </div>
                            <a href="{{ route('sdk.webhooks') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-play me-2"></i>Explore Webhooks API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mt-5 pt-5">
        <div class="col-12">
            <div class="bg-light rounded-3 p-5">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h3 class="h2 mb-4">What You Can Test</h3>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-code-branch fa-2x mb-3 text-primary"></i>
                                    <h5>Real API Calls</h5>
                                    <p class="text-muted small">
                                        All demonstrations make actual calls to the Applax Gateway using your live API credentials.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-copy fa-2x mb-3 text-primary"></i>
                                    <h5>Copy Code Examples</h5>
                                    <p class="text-muted small">
                                        Every API call includes ready-to-use code examples that you can copy to your project.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-shield-alt fa-2x mb-3 text-primary"></i>
                                    <h5>Safe Test Environment</h5>
                                    <p class="text-muted small">
                                        All operations run in test mode with sandbox data - no real transactions are processed.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw API Section -->
    <div class="row mt-5 pt-5">
        <div class="col-12">
            <div class="bg-gradient rounded-3 p-5 text-gray-700 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="pe-lg-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-rocket fa-2x me-3 opacity-75"></i>
                                <h3 class="h2 mb-0 fw-bold">Raw API Access</h3>
                            </div>
                            <p class="lead mb-4">
                                Unlock unlimited possibilities with direct access to any Appla-X Gate API endpoint.
                                The Raw API functions provide immediate access to new features, beta endpoints, and
                                complete customization for advanced integrations.
                            </p>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>Access to <strong>Brands, Subscriptions, Taxes & Charges</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>Future-proof with <strong>any new API endpoints</strong></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>Full HTTP method support <strong>(GET, POST, PUT, PATCH, DELETE)</strong></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>Complete error handling & retry logic</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="https://github.com/applax-dev/gate-sdk/blob/master/docs/raw-api-access.md"
                                   target="_blank" class="btn btn-primary btn-lg">
                                    <i class="fas fa-book me-2"></i>Complete Documentation
                                </a>
                                <a href="https://docs.appla-x.com/" target="_blank" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-external-link-alt me-2"></i>API Reference
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <div class="bg-dark rounded-3 p-4 code-showcase">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex gap-1">
                                        <span class="bg-danger rounded-circle" style="width: 12px; height: 12px;"></span>
                                        <span class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></span>
                                        <span class="bg-success rounded-circle" style="width: 12px; height: 12px;"></span>
                                    </div>
                                    <span class="ms-3 text-muted small">Raw API Example</span>
                                </div>
                                <pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-info">// Create a new brand</span>
<span class="text-warning">$brand</span> = <span class="text-warning">$gateSDK</span>-><span class="text-success">rawPost</span>(<span class="text-primary">'/brands/'</span>, [
    <span class="text-primary">'name'</span> => <span class="text-primary">'My Amazing Brand'</span>,
    <span class="text-primary">'website'</span> => <span class="text-primary">'https://mybrand.com'</span>,
    <span class="text-primary">'logo_url'</span> => <span class="text-primary">'https://mybrand.com/logo.png'</span>
]);

<span class="text-info">// Create subscription with taxes</span>
<span class="text-warning">$subscription</span> = <span class="text-warning">$gateSDK</span>-><span class="text-success">rawPost</span>(<span class="text-primary">'/subscriptions/'</span>, [
    <span class="text-primary">'client'</span> => [<span class="text-primary">'email'</span> => <span class="text-primary">'customer@example.com'</span>],
    <span class="text-primary">'plan'</span> => <span class="text-primary">'premium-monthly'</span>,
    <span class="text-primary">'amount'</span> => <span class="text-light">29.99</span>,
    <span class="text-primary">'currency'</span> => <span class="text-primary">'EUR'</span>,
    <span class="text-primary">'taxes'</span> => [<span class="text-light">21.0</span>] <span class="text-info">// VAT rate</span>
]);

<span class="text-info">// Universal method for any endpoint</span>
<span class="text-warning">$response</span> = <span class="text-warning">$gateSDK</span>-><span class="text-success">raw</span>(
    <span class="text-primary">'GET'</span>,
    <span class="text-primary">'/brands/'</span>,
    <span class="text-light">null</span>,
    [<span class="text-primary">'limit'</span> => <span class="text-light">10</span>]
);</code></pre>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success">New in v1.2.0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Background decoration -->
                <!-- div class="position-absolute top-0 end-0 opacity-10">
                    <i class="fas fa-code" style="font-size: 200px; transform: rotate(15deg);"></i>
                </div -->
            </div>
        </div>
    </div>

    <!-- Getting Started Section -->
    <div class="row mt-5 pt-4">
        <div class="col-12">
            <div class="text-center">
                <h3 class="h4 mb-4">Ready to Get Started?</h3>
                <p class="text-muted mb-4">
                    Choose any API above to begin exploring the Applax Gate SDK capabilities.
                    Each section includes interactive forms, live API responses, and code examples.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('sdk.products') }}" class="btn btn-primary btn-lg">
                        Start with Products API
                    </a>
                    <a href="https://docs.appla-x.com/" target="_blank" class="btn btn-outline-primary btn-lg">
                        Read Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.feature-item {
    text-align: center;
}

.icon-wrapper {
    flex-shrink: 0;
}
</style>
@endsection
