@extends('layouts.app')

@section('title', 'Card Payments')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <i class="fas fa-credit-card display-1 text-primary"></i>
            </div>

            <h1 class="display-4 fw-bold text-primary mb-3">
                Card Payments
            </h1>

            <p class="lead text-muted mb-4">
                Experience our comprehensive card payment solution through live demonstrations. The Shop Demo showcases
                the complete customer journey - from product selection to secure card checkout with real payment processing.
                The Admin Demo provides merchant tools for transaction monitoring, payment management, and detailed analytics.
            </p>

            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('shop.products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart me-2"></i>Shop Demo
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-chart-bar me-2"></i>Admin Demo
                </a>
            </div>

            <hr class="my-5">

            <div class="row text-start">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Shop Demo Features</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Product catalog browsing</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Shopping cart management</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Secure card checkout form</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Real-time payment processing</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Order confirmation & receipts</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Admin Demo Features</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Transaction monitoring dashboard</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Payment history & analytics</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Customer order management</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Refund & capture operations</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Detailed payment reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection