@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Payment Failed
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="text-danger mb-3">Payment Could Not Be Processed</h5>

                    <p class="text-muted mb-4">
                        We're sorry, but your payment could not be processed at this time. This may be due to:
                    </p>

                    <div class="text-start mb-4">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i> Insufficient funds</li>
                            <li class="mb-2"><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i> Card declined by bank</li>
                            <li class="mb-2"><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i> Incorrect card details</li>
                            <li class="mb-2"><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i> Network connection issues</li>
                        </ul>
                    </div>

                    @if(request('error'))
                        <div class="alert alert-warning">
                            <strong>Error Details:</strong> {{ ucfirst(str_replace('_', ' ', request('error'))) }}
                        </div>
                    @endif

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        @if($lastOrder && $lastOrder->canBePaid())
                            @if($lastOrder->getPaymentUrl())
                                <a href="{{ $lastOrder->getPaymentUrl() }}" class="btn btn-primary me-md-2">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Retry Payment
                                </a>
                            @else
                                <a href="{{ route('shop.checkout.step', ['step' => 'payment', 'retry_order' => $lastOrder->id]) }}" class="btn btn-primary me-md-2">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Retry Payment
                                </a>
                            @endif
                        @else
                            <a href="{{ route('shop.products.index') }}" class="btn btn-primary me-md-2">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Start New Order
                            </a>
                        @endif

                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-receipt me-2"></i>
                            View Orders
                        </a>

                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>
                            Continue Shopping
                        </a>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <p class="text-muted small mb-0">
                            Need help? Contact our support team for assistance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection