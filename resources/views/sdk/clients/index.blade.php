@extends('layouts.app')

@section('title', 'Clients API - SDK Showcase')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1" style="color: #3b4151;">
                        <i class="bi bi-people me-2"></i>Clients API Showcase
                    </h1>
                    <p class="text-muted mb-0">
                        Live demonstration of Applax Gate SDK Client management endpoints and customer data operations
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
                    <li class="breadcrumb-item active" aria-current="page">Clients API</li>
                </ol>
            </nav>

            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Live SDK Integration:</strong> This page demonstrates actual calls to the Applax Gateway for client management,
                        customer data operations, and profile administration using our GateSDKService wrapper.
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
                        <a href="#create-client" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>createClient()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Create a new client profile</small>
                        </a>

                        <a href="#get-clients" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getClients()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Retrieve clients list with filters</small>
                        </a>

                        <a href="#get-client" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>getClient()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Get single client details by ID</small>
                        </a>

                        <a href="#update-client" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">PUT</span>
                                <code>updateClient()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Update complete client profile</small>
                        </a>

                        <a href="#partial-update-client" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">PATCH</span>
                                <code>partialUpdateClient()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Update specific client fields</small>
                        </a>

                        <a href="#delete-client" class="list-group-item list-group-item-action method-nav-link">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">DELETE</span>
                                <code>deleteClient()</code>
                            </div>
                            <small class="text-muted d-block mt-1">Delete client profile</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SDK Demonstrations -->
        <div class="col-lg-8">
            <!-- Create Client -->
            <div id="create-client" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-success me-2">POST</span>
                        Create Client
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a new client profile in the Applax Gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('create-client-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="create-client-code" class="language-php">$clientData = [
    'email' => 'john.doe@example.com',
    'phone' => '371-12345678',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'birth_date' => '1990-01-15'
];

$result = $gateSDK->createClient($clientData);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="createClientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="client_email" name="email" required
                                           placeholder="john.doe@example.com" maxlength="254">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="client_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="client_phone" name="phone" required
                                           placeholder="371-12345678" maxlength="32">
                                    <div class="form-text">Format: country-code-phone-number</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="client_first_name" name="first_name"
                                           placeholder="John" maxlength="63">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="client_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="client_last_name" name="last_name"
                                           placeholder="Doe" maxlength="63">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="client_birth_date" name="birth_date">
                                    <div class="form-text">Format: YYYY-MM-DD</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>Create Client in Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="create-client-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Clients -->
            <div id="get-clients" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Clients
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve clients with optional filters from the Applax Gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-clients-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-clients-code" class="language-php">$filters = [
    'cursor' => 'next_page_cursor_string',  // Optional: for pagination
    'filter_email' => 'john@example.com',
    'filter_phone' => '371-12345678',
    'search_query' => 'search query'
];

$result = $gateSDK->getClients($filters);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="getClientsForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="cursor" class="form-label">Cursor (for pagination)</label>
                                    <input type="text" class="form-control" id="cursor" name="cursor" placeholder="Leave empty for first page">
                                    <div class="form-text">Use cursor from previous response for pagination</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="filter_email" class="form-label">Filter by Email</label>
                                    <input type="email" class="form-control" id="filter_email" name="filter_email" placeholder="john@example.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="filter_phone" class="form-label">Filter by Phone</label>
                                    <input type="text" class="form-control" id="filter_phone" name="filter_phone" placeholder="371-12345678">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="search_query" class="form-label">Search Query</label>
                                    <input type="text" class="form-control" id="search_query" name="q" placeholder="Full-text search">
                                    <div class="form-text">Search across client fields</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Clients from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-clients-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Single Client -->
            <div id="get-client" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-primary me-2">GET</span>
                        Get Client
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Retrieve details of a specific client by ID.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('get-client-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="get-client-code" class="language-php">$clientId = 'cl_abc123def456';
$result = $gateSDK->getClient($clientId);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="getClientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="get_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="get_client_id" name="client_id" required
                                           placeholder="e.g., cl_abc123def456">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Get Client from Gateway
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="get-client-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Update Client -->
            <div id="update-client" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-warning me-2">PUT</span>
                        Update Client
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Update complete client profile with all required fields.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('update-client-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="update-client-code" class="language-php">$clientId = 'cl_abc123def456';
$clientData = [
    'email' => 'updated.email@example.com',
    'phone' => '371-98765432',
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'birth_date' => '1985-05-20'
];

$result = $gateSDK->updateClient($clientId, $clientData);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="updateClientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="update_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="update_client_id" name="client_id" required
                                           placeholder="e.g., cl_abc123def456">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="update_client_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="update_client_email" name="email" required
                                           placeholder="updated.email@example.com" maxlength="254">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update_client_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="update_client_phone" name="phone" required
                                           placeholder="371-98765432" maxlength="32">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="update_client_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="update_client_first_name" name="first_name"
                                           placeholder="Jane" maxlength="63">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update_client_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="update_client_last_name" name="last_name"
                                           placeholder="Smith" maxlength="63">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="update_client_birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="update_client_birth_date" name="birth_date">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Update Client
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="update-client-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Partial Update Client -->
            <div id="partial-update-client" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-info me-2">PATCH</span>
                        Partial Update Client
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Update specific client fields without affecting others.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('partial-update-client-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="partial-update-client-code" class="language-php">$clientId = 'cl_abc123def456';
$partialData = [
    'first_name' => 'UpdatedName',
    'last_name' => 'UpdatedSurname'
];

$result = $gateSDK->partialUpdateClient($clientId, $partialData);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="partialUpdateClientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="partial_update_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="partial_update_client_id" name="client_id" required
                                           placeholder="e.g., cl_abc123def456">
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> Only fill in the fields you want to update. Empty fields will be ignored.
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="partial_update_client_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="partial_update_client_email" name="email"
                                           placeholder="new.email@example.com" maxlength="254">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="partial_update_client_phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="partial_update_client_phone" name="phone"
                                           placeholder="371-11111111" maxlength="32">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="partial_update_client_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="partial_update_client_first_name" name="first_name"
                                           placeholder="UpdatedName" maxlength="63">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="partial_update_client_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="partial_update_client_last_name" name="last_name"
                                           placeholder="UpdatedSurname" maxlength="63">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="partial_update_client_birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="partial_update_client_birth_date" name="birth_date">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-pencil-square me-1"></i>Partial Update Client
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="partial-update-client-response" class="response-container" style="display: none;">
                        <h6>Gateway Response</h6>
                        <pre><code class="response-content"></code></pre>
                    </div>
                </div>
            </div>

            <!-- Delete Client -->
            <div id="delete-client" class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="badge bg-danger me-2">DELETE</span>
                        Delete Client
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will permanently delete the client profile. This cannot be undone.
                    </div>
                    <p class="text-muted">Permanently remove a client profile from the gateway.</p>

                    <!-- Code Example -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Code Example</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('delete-client-code')">
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <pre><code id="delete-client-code" class="language-php">$clientId = 'cl_abc123def456';
$result = $gateSDK->deleteClient($clientId);</code></pre>
                    </div>

                    <!-- Form -->
                    <div class="mb-3">
                        <form id="deleteClientForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="delete_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="delete_client_id" name="client_id" required
                                           placeholder="e.g., cl_abc123def456">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete Client
                            </button>
                        </form>
                    </div>

                    <!-- Response Display -->
                    <div id="delete-client-response" class="response-container" style="display: none;">
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
    // Create Client Form
    document.getElementById('createClientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        // Handle all fields
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                data[key] = value;
            }
        }

        makeApiRequest('/sdk/clients/create', data, 'create-client-response', 'Creating client...');
    });

    // Get Clients Form
    document.getElementById('getClientsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (value) data[key] = value;
        }

        makeApiRequest('/sdk/clients/get', data, 'get-clients-response', 'Getting clients...');
    });

    // Get Single Client Form
    document.getElementById('getClientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        makeApiRequest('/sdk/clients/get-single', data, 'get-client-response', 'Getting client...');
    });

    // Update Client Form
    document.getElementById('updateClientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                data[key] = value;
            }
        }

        makeApiRequest('/sdk/clients/update', data, 'update-client-response', 'Updating client...');
    });

    // Partial Update Client Form
    document.getElementById('partialUpdateClientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};

        // Only include non-empty fields
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                data[key] = value;
            }
        }

        // Check if at least one field besides client_id is provided
        if (Object.keys(data).length <= 1) {
            alert('Please provide at least one field to update.');
            return;
        }

        makeApiRequest('/sdk/clients/partial-update', data, 'partial-update-client-response', 'Updating client...');
    });

    // Delete Client Form
    document.getElementById('deleteClientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
            return;
        }

        const formData = new FormData(this);
        const data = {};

        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        makeApiRequest('/sdk/clients/delete', data, 'delete-client-response', 'Deleting client...');
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

        responseContainer.className = 'response-container success-response';
        responseContainer.style.display = 'block';

        // Scroll to response
        responseContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    })
    .catch(error => {
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
    margin-top: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.response-container.success-response {
    background-color: #d1edff;
    border-color: #0066cc;
}

.response-container.error-response {
    background-color: #f8d7da;
    border-color: #dc3545;
}

.response-container h6 {
    padding: 10px 15px 0;
    margin-bottom: 0;
    font-weight: 600;
    color: #495057;
}

.response-container pre {
    margin: 0;
    border-radius: 0 0 7px 7px;
    border: none;
    background-color: transparent;
    padding: 10px 15px;
}

.response-container.success-response pre {
    background-color: #e7f3ff;
}

.response-container.error-response pre {
    background-color: #f5c6cb;
}

.method-nav-link:hover {
    background-color: #f8f9fa;
}

.sticky-top {
    position: sticky;
}

.hover-card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
@endsection