@extends('layouts.app')

@section('title', 'Webhooks API - SDK Showcase')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1" style="color: #3b4151;">
                        <i class="bi bi-link-45deg me-2"></i>Webhooks API Showcase
                    </h1>
                    <p class="text-muted mb-0">
                        Live demonstration of Applax Gate SDK Webhook endpoints and event subscription management methods
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
                    <li class="breadcrumb-item active" aria-current="page">Webhooks API</li>
                </ol>
            </nav>

            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Live SDK Integration:</strong> This page demonstrates actual calls to the Applax Gateway for webhook management,
                        event subscriptions, and notification configuration using our GateSDKService wrapper.
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
                        <a href="#create-webhook" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>createWebhook()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Create a new webhook endpoint</small>
                        </a>

                        <a href="#get-webhooks" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getWebhooks()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Retrieve webhooks list with filters</small>
                        </a>

                        <a href="#get-webhook" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getWebhook()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Get single webhook details by ID</small>
                        </a>

                        <a href="#update-webhook" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">PUT</span>
                                <code>updateWebhook()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Update webhook configuration</small>
                        </a>


                        <a href="#delete-webhook" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">DELETE</span>
                                <code>deleteWebhook()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Delete webhook endpoint</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SDK Demonstrations -->
        <div class="col-lg-8">
            <!-- Create Webhook -->
            <div id="create-webhook" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-success me-2">POST</span>
                        Create Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a new webhook endpoint to receive event notifications in the Applax Gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('create-webhook-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="create-webhook-code" class="language-php">$webhookData = [
    'title' => 'Payment Notifications Webhook',
    'url' => 'https://your-domain.com/webhook/payment',
    'events' => ['order.payment_success', 'order.payment_failure'],
    'version' => 'v0.6',
    'is_test' => false
];

$result = $gateSDK->createWebhook($webhookData);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="createWebhookForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="webhook_title" class="form-label">Webhook Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="webhook_title" name="title" required
                                           placeholder="Payment Notifications Webhook">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="webhook_version" class="form-label">Version</label>
                                    <select class="form-select" id="webhook_version" name="version">
                                        <option value="v0.6" selected>v0.6 (Recommended)</option>
                                        <option value="v0.5">v0.5 (Legacy)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="webhook_url" class="form-label">Webhook URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="webhook_url" name="url" required
                                           placeholder="https://your-domain.com/webhook/payment">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Event Configuration <span class="text-danger">*</span></label>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="event_mode" id="all_events" value="all_events" checked>
                                        <label class="form-check-label" for="all_events">
                                            <strong>All Events</strong> - Subscribe to all available event types
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="event_mode" id="specific_events" value="specific">
                                        <label class="form-check-label" for="specific_events">
                                            <strong>Specific Events</strong> - Choose individual events
                                        </label>
                                    </div>
                                </div>
                                <div id="events-section" style="display: none;">
                                    <label class="form-label">Select Event Types</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Order Events</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.issued" id="event_order_issued" name="events[]">
                                                <label class="form-check-label" for="event_order_issued">order.issued</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.payment_success" id="event_order_payment_success" name="events[]">
                                                <label class="form-check-label" for="event_order_payment_success">order.payment_success</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.payment_failure" id="event_order_payment_failure" name="events[]">
                                                <label class="form-check-label" for="event_order_payment_failure">order.payment_failure</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.hold" id="event_order_hold" name="events[]">
                                                <label class="form-check-label" for="event_order_hold">order.hold</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.canceled" id="event_order_canceled" name="events[]">
                                                <label class="form-check-label" for="event_order_canceled">order.canceled</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_viewed" id="event_order_invoice_viewed" name="events[]">
                                                <label class="form-check-label" for="event_order_invoice_viewed">order.invoice_viewed</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>More Order Events</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_refunded" id="event_order_invoice_refunded" name="events[]">
                                                <label class="form-check-label" for="event_order_invoice_refunded">order.invoice_refunded</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_reversal" id="event_order_invoice_reversal" name="events[]">
                                                <label class="form-check-label" for="event_order_invoice_reversal">order.invoice_reversal</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.refund_failure" id="event_order_refund_failure" name="events[]">
                                                <label class="form-check-label" for="event_order_refund_failure">order.refund_failure</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.received" id="event_order_received" name="events[]">
                                                <label class="form-check-label" for="event_order_received">order.received</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.expired" id="event_order_expired" name="events[]">
                                                <label class="form-check-label" for="event_order_expired">order.expired</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="transfer.withdrawn" id="event_transfer_withdrawn" name="events[]">
                                                <label class="form-check-label" for="event_transfer_withdrawn">transfer.withdrawn</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="webhook_is_test" name="is_test">
                                        <label class="form-check-label" for="webhook_is_test">
                                            Test Mode
                                            <small class="text-muted d-block">Trigger callbacks for test events only</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>Create Webhook in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="create-webhook-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Webhooks -->
            <div id="get-webhooks" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Webhooks
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve webhooks with optional filters from the Applax Gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-webhooks-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-webhooks-code" class="language-php">$filters = [
    'limit' => 50,
    'offset' => 0,
    'event_type' => 'payment.created',
    'status' => 'active'
];

$result = $gateSDK->getWebhooks($filters);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="getWebhooksForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="limit" class="form-label">Limit</label>
                                    <input type="number" class="form-control" id="limit" name="limit" placeholder="50" max="50" min="1">
                                    <div class="form-text">Maximum 50 records</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="offset" class="form-label">Offset</label>
                                    <input type="number" class="form-control" id="offset" name="offset" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="event_type" class="form-label">Event Type</label>
                                    <select class="form-select" id="event_type" name="event_type">
                                        <option value="">All Events</option>
                                        <option value="payment.created">payment.created</option>
                                        <option value="payment.authorized">payment.authorized</option>
                                        <option value="payment.captured">payment.captured</option>
                                        <option value="payment.failed">payment.failed</option>
                                        <option value="payment.refunded">payment.refunded</option>
                                        <option value="order.completed">order.completed</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="webhook_status" class="form-label">Status</label>
                                    <select class="form-select" id="webhook_status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Webhooks from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-webhooks-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Single Webhook -->
            <div id="get-webhook" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Single Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve details of a specific webhook by ID.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-webhook-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-webhook-code" class="language-php">$webhookId = 'wh_abc123def456';
$result = $gateSDK->getWebhook($webhookId);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="getWebhookForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="get_webhook_id" class="form-label">Webhook ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="get_webhook_id" name="webhook_id" required
                                           placeholder="e.g., wh_abc123def456">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Webhook from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-webhook-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Update Webhook -->
            <div id="update-webhook" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-warning me-2">PUT</span>
                        Update Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Update an existing webhook configuration.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('update-webhook-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="update-webhook-code" class="language-php">$webhookId = 'wh_abc123def456';
$webhookData = [
    'title' => 'Updated Payment Notifications Webhook',
    'url' => 'https://new-domain.com/webhook/payment',
    'events' => ['order.payment_success', 'order.payment_failure'],
    'version' => 'v0.6',
    'is_test' => false
];

$result = $gateSDK->updateWebhook($webhookId, $webhookData);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="updateWebhookForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="update_webhook_id" class="form-label">Webhook ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="update_webhook_id" name="webhook_id" required
                                           placeholder="e.g., wh_abc123def456">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="update_webhook_title" class="form-label">Webhook Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="update_webhook_title" name="title" required
                                           placeholder="Updated Payment Notifications Webhook">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update_webhook_version" class="form-label">Version</label>
                                    <select class="form-select" id="update_webhook_version" name="version">
                                        <option value="v0.6" selected>v0.6 (Recommended)</option>
                                        <option value="v0.5">v0.5 (Legacy)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="update_webhook_url" class="form-label">Webhook URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="update_webhook_url" name="url" required
                                           placeholder="https://your-domain.com/webhook/payment">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Event Configuration <span class="text-danger">*</span></label>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="update_event_mode" id="update_all_events" value="all_events" checked>
                                        <label class="form-check-label" for="update_all_events">
                                            <strong>All Events</strong> - Subscribe to all available event types
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="update_event_mode" id="update_specific_events" value="specific">
                                        <label class="form-check-label" for="update_specific_events">
                                            <strong>Specific Events</strong> - Choose individual events
                                        </label>
                                    </div>
                                </div>
                                <div id="update-events-section" style="display: none;">
                                    <label class="form-label">Select Event Types</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Order Events</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.issued" id="update_event_order_issued" name="events[]">
                                                <label class="form-check-label" for="update_event_order_issued">order.issued</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.payment_success" id="update_event_order_payment_success" name="events[]">
                                                <label class="form-check-label" for="update_event_order_payment_success">order.payment_success</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.payment_failure" id="update_event_order_payment_failure" name="events[]">
                                                <label class="form-check-label" for="update_event_order_payment_failure">order.payment_failure</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.hold" id="update_event_order_hold" name="events[]">
                                                <label class="form-check-label" for="update_event_order_hold">order.hold</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.canceled" id="update_event_order_canceled" name="events[]">
                                                <label class="form-check-label" for="update_event_order_canceled">order.canceled</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_viewed" id="update_event_order_invoice_viewed" name="events[]">
                                                <label class="form-check-label" for="update_event_order_invoice_viewed">order.invoice_viewed</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>More Order Events</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_refunded" id="update_event_order_invoice_refunded" name="events[]">
                                                <label class="form-check-label" for="update_event_order_invoice_refunded">order.invoice_refunded</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.invoice_reversal" id="update_event_order_invoice_reversal" name="events[]">
                                                <label class="form-check-label" for="update_event_order_invoice_reversal">order.invoice_reversal</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.refund_failure" id="update_event_order_refund_failure" name="events[]">
                                                <label class="form-check-label" for="update_event_order_refund_failure">order.refund_failure</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.received" id="update_event_order_received" name="events[]">
                                                <label class="form-check-label" for="update_event_order_received">order.received</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="order.expired" id="update_event_order_expired" name="events[]">
                                                <label class="form-check-label" for="update_event_order_expired">order.expired</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="transfer.withdrawn" id="update_event_transfer_withdrawn" name="events[]">
                                                <label class="form-check-label" for="update_event_transfer_withdrawn">transfer.withdrawn</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="update_webhook_is_test" name="is_test">
                                        <label class="form-check-label" for="update_webhook_is_test">
                                            Test Mode
                                            <small class="text-muted d-block">Trigger callbacks for test events only</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Update Webhook
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="update-webhook-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>


            <!-- Delete Webhook -->
            <div id="delete-webhook" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-danger me-2">DELETE</span>
                        Delete Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will permanently delete the webhook. This cannot be undone.
                    </div>
                    <p class="text-muted">Permanently remove a webhook endpoint.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('delete-webhook-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="delete-webhook-code" class="language-php">$webhookId = 'wh_abc123def456';
$result = $gateSDK->deleteWebhook($webhookId);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="deleteWebhookForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="delete_webhook_id" class="form-label">Webhook ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="delete_webhook_id" name="webhook_id" required
                                           placeholder="e.g., wh_abc123def456">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete Webhook
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="delete-webhook-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event mode radio button handling
    const eventModeRadios = document.querySelectorAll('input[name="event_mode"]');
    const eventsSection = document.getElementById('events-section');

    eventModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'specific') {
                eventsSection.style.display = 'block';
            } else {
                eventsSection.style.display = 'none';
            }
        });
    });

    // Create Webhook Form
    document.getElementById('createWebhookForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        // Handle regular fields
        for (let [key, value] of formData.entries()) {
            if (key === 'events[]' || key === 'event_mode') continue; // Handle separately
            if (key === 'is_test') {
                data[key] = formData.has('is_test');
            } else {
                data[key] = value;
            }
        }

        // Handle event configuration
        const eventMode = formData.get('event_mode');
        if (eventMode === 'all_events') {
            data.all_events = true;
        } else {
            const events = formData.getAll('events[]');
            if (events.length === 0) {
                alert('Please select at least one event type.');
                return;
            }
            data.events = events;
        }

        makeApiRequest('/sdk/webhooks/create', data, 'create-webhook-response', 'Creating webhook...');
    });

    // Get Webhooks Form
    document.getElementById('getWebhooksForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (value) data[key] = value;
        }
        makeApiRequest('/sdk/webhooks/get', data, 'get-webhooks-response', 'Getting webhooks...');
    });

    // Get Single Webhook Form
    document.getElementById('getWebhookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        makeApiRequest('/sdk/webhooks/get-single', data, 'get-webhook-response', 'Getting webhook...');
    });

    // Update Webhook Form
    // Update webhook event mode radio button handling
    const updateEventModeRadios = document.querySelectorAll('input[name="update_event_mode"]');
    const updateEventsSection = document.getElementById('update-events-section');

    updateEventModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'specific') {
                updateEventsSection.style.display = 'block';
            } else {
                updateEventsSection.style.display = 'none';
            }
        });
    });

    document.getElementById('updateWebhookForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        // Handle regular fields
        for (let [key, value] of formData.entries()) {
            if (key === 'events[]' || key === 'update_event_mode') continue; // Handle separately
            if (key === 'is_test') {
                data[key] = formData.has('is_test');
            } else {
                data[key] = value;
            }
        }

        // Handle event configuration
        const eventMode = formData.get('update_event_mode');
        if (eventMode === 'all_events') {
            data.all_events = true;
        } else {
            const events = formData.getAll('events[]');
            if (events.length === 0) {
                alert('Please select at least one event type.');
                return;
            }
            data.events = events;
        }

        makeApiRequest('/sdk/webhooks/update', data, 'update-webhook-response', 'Updating webhook...');
    });


    // Delete Webhook Form
    document.getElementById('deleteWebhookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this webhook? This action cannot be undone.')) {
            return;
        }
        const formData = new FormData(this);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        makeApiRequest('/sdk/webhooks/delete', data, 'delete-webhook-response', 'Deleting webhook...');
    });
});

function makeApiRequest(url, data, responseContainerId, loadingMessage) {
    const responseContainer = document.getElementById(responseContainerId);
    const responseContent = responseContainer.querySelector('.response-content');

    responseContainer.style.display = 'block';
    responseContent.textContent = loadingMessage;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
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

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary feedback
        const button = element.parentNode.querySelector('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>

<style>
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
}
</style>
@endsection