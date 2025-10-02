<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Applax Payment Showcase') - {{ config('app.name') }}</title>

    <link rel="apple-touch-icon" sizes="57x57" href="{{asset("/images/icon/apple-icon-57x57.png")}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{asset("/images/icon/apple-icon-60x60.png")}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset("/images/icon/apple-icon-72x72.png")}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset("/images/icon/apple-icon-76x76.png")}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset("/images/icon/apple-icon-114x114.png")}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{asset("/images/icon/apple-icon-120x120.png")}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{asset("/images/icon/apple-icon-144x144.png")}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{asset("/images/icon/apple-icon-152x152.png")}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset("/images/icon/apple-icon-180x180.png")}}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{asset("/images/icon/android-icon-192x192.png")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("/images/icon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{asset("/images/icon/favicon-96x96.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("/images/icon/favicon-16x16.png")}}">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{asset("/images/icon/ms-icon-144x144.png")}}">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{asset('css/all.min.css')}}" rel="stylesheet" />
    <!-- International Telephone Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --bs-primary: #328a75;
            --bs-primary-rgb: 140, 28, 3;
            --bs-text-color: #3b4151;
            --bs-text-rgb: 59, 65, 81;
            --bs-font-family: 'Inter', sans-serif;
        }

        body {
            font-family: var(--bs-font-family);
            color: var(--bs-text-color);
            background-color: #f8f9fa;
        }

        /* Primary color overrides */
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #024938;
            border-color: #024938;
        }

        .btn-outline-primary {
            color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .text-primary {
            color: var(--bs-primary) !important;
        }

        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .border-primary {
            border-color: var(--bs-primary) !important;
        }

        .nav-link.active {
            color: var(--bs-primary) !important;
        }

        .navbar-brand {
            color: var(--bs-primary) !important;
        }

        .navbar-brand:hover {
            color: #024938 !important;
        }

        /* Custom styling */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        .navbar-brand img {
            height: 35px;
            width: auto;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        .badge.bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .alert-demo {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
            border-left: 4px solid #ffc107;
        }

        .demo-badge {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .payment-method-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .payment-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,.12);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .footer {
            background-color: var(--bs-primary);
            color: white;
            margin-top: auto;
        }

        .main-content {
            min-height: calc(100vh - 200px);
        }

        /* Code block styling */
        .code-block {
            background-color: #2d3748;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.375rem;
            overflow-x: auto;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .breadcrumb-item a {
            color: rgb(25 135 84) !important;
            text-decoration: underline;
        }
        .code-block .keyword { color: #81e6d9; }
        .code-block .string { color: #90cdf4; }
        .code-block .comment { color: #a0aec0; }
        .code-block .function { color: #fbb6ce; }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="Applax" class="me-2">
                <span class="fw-bold">Payment Showcase</span>
                <span class="demo-badge ms-2">DEMO</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.products.*') ? 'active' : '' }}" href="{{ route('shop.products.index') }}">
                            <i class="bi bi-shop me-1"></i>Shop Demo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sdk.*') ? 'active' : '' }}" href="{{ route('sdk.showcase') }}">
                            <i class="bi bi-code-slash me-1"></i>SDK Showcase
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('payment.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-credit-card me-1"></i>Payment Methods
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('payment.cards') }}"><i class="bi bi-credit-card-2-front me-2"></i>Cards</a></li>
                            <li><a class="dropdown-item" href="{{ route('payment.digital-wallets') }}"><i class="bi bi-phone me-2"></i>Digital Wallets</a></li>
                            <li><a class="dropdown-item" href="{{ route('payment.alternative') }}"><i class="bi bi-bank me-2"></i>Alternative Methods</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i>Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt me-2"></i>Orders</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-people me-2"></i>Customers</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam me-2"></i>Products</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}"><i class="bi bi-credit-card me-2"></i>Payments</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.webhooks.*') ? 'active' : '' }}" href="{{ route('admin.webhooks.index') }}"><i class="bi bi-arrow-left-right me-2"></i>Webhooks</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shop.cart.index') }}">
                            <i class="bi bi-cart me-1"></i>Cart
                            @if(session('cart_count', 0) > 0)
                                <span class="badge bg-primary rounded-pill">{{ session('cart_count') }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Demo Alert -->
    @if(!request()->routeIs('admin.*'))
    <div class="alert alert-demo border-0 rounded-0 mb-0 text-center">
        <div class="container">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Demo Environment:</strong> This is a showcase of the Applax Gate SDK. All payments use sandbox mode with test data.
            <a href="{{ route('sdk.showcase') }}" class="alert-link ms-2">View SDK Documentation <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4 mt-5">
        <!-- Quick Start -->
        <section class="bg-primary text-white py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="fw-bold mb-3">Ready to integrate Applax Gate SDK?</h3>
                        <p class="lead mb-0">
                            Install the package and start building your payment solution today.
                            Use this showcase as your complete integration guide.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-grid gap-2 d-md-block">
                            <a href="https://github.com/applax-dev/gate-sdk" class="btn btn-light btn-lg" target="_blank">
                                <i class="bi bi-github me-2"></i>GitHub Repository
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <h5 class="text-white mb-3">Applax Payment Gateway</h5>
                    <p class="text-white-50 mb-2">
                        Comprehensive payment solution with support for multiple payment methods,
                        enterprise security, and developer-friendly integration.
                    </p>
                    <div class="d-flex">
                        <a href="https://github.com/applax-dev/gate-sdk" class="text-white me-3" target="_blank">
                            <i class="bi bi-github"></i>
                        </a>
                        <a href="https://docs.appla-x.com/" class="text-white me-3" target="_blank">
                            <i class="bi bi-book"></i>
                        </a>
                        <a href="https://gate.appla-x.com/" class="text-white" target="_blank">
                            <i class="bi bi-globe"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <h6 class="text-white mb-3">Demo Features</h6>
                    <ul class="list-unstyled text-white-50">
                        <li><a href="{{ route('shop.products.index') }}" class="text-white-50 text-decoration-none">Shopping Demo</a></li>
                        <li><a href="{{ route('payment.cards') }}" class="text-white-50 text-decoration-none">Card Payments</a></li>
                        <li><a href="{{ route('payment.digital-wallets') }}" class="text-white-50 text-decoration-none">Digital Wallets</a></li>
                        <li><a href="{{ route('admin.dashboard') }}" class="text-white-50 text-decoration-none">Admin Dashboard</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <h6 class="text-white mb-3">SDK Features</h6>
                    <ul class="list-unstyled text-white-50">
                        <li><a href="{{ route('sdk.products') }}" class="text-white-50 text-decoration-none">Products API</a></li>
                        <li><a href="{{ route('sdk.orders') }}" class="text-white-50 text-decoration-none">Orders API</a></li>
                        <li><a href="{{ route('sdk.clients') }}" class="text-white-50 text-decoration-none">Clients API</a></li>
                        <li><a href="{{ route('sdk.webhooks') }}" class="text-white-50 text-decoration-none">Webhooks API</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-3">
                    <h6 class="text-white mb-3">Integration Support</h6>
                    <p class="text-white-50 mb-2">
                        Need help integrating? Check our documentation or visit our GitHub repository.
                    </p>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-0">
                            <i class="bi bi-terminal text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0" readonly
                               value="composer require applax-dev/gate-sdk">
                        <button class="btn btn-light border-0" type="button" onclick="copyToClipboard(this)">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-white-50">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-white-50 mb-0">&copy; {{ date('Y') }} Applax Payment Gateway. Built for developers.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-white-50 small">
                        Powered by <strong class="text-white">applax-dev/gate-sdk</strong> v1.0
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- International Telephone Input -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <!-- Custom JS -->
    <script>
        function copyToClipboard(button) {
            const input = button.previousElementSibling;
            input.select();
            document.execCommand('copy');

            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check text-success"></i>';

            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 2000);
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-demo)');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-dismissible')) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                }
            });
        });

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    @stack('scripts')
    @include('sweetalert::alert')
</body>
</html>
