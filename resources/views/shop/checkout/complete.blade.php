@extends('layouts.app')

@section('title', 'Order Complete - Thank You!')

@section('content')
<div class="container my-5">
    <!-- Status Header -->
    <div class="text-center mb-5">
        <div class="mb-4">
            @if($order->status === 'paid')
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; background-color: #28a745; color: white;">
                    <i class="fas fa-check fa-3x"></i>
                </div>
                <h1 class="h2 mb-3" style="color: #3b4151;">Order Complete!</h1>
                <p class="lead text-muted">Thank you for your demo order. Your payment has been processed successfully.</p>
            @elseif(in_array($order->status, ['failed', 'cancelled', 'expired', 'rejected']))
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; background-color: #dc3545; color: white;">
                    <i class="fas fa-times fa-3x"></i>
                </div>
                <h1 class="h2 mb-3" style="color: #dc3545;">Payment {{ ucfirst($order->status) }}</h1>
                <p class="lead text-muted">Your payment was {{ $order->status }}. You can try again or contact support if needed.</p>
            @else
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; background-color: #ffc107; color: white;">
                    <i class="fas fa-clock fa-3x"></i>
                </div>
                <h1 class="h2 mb-3" style="color: #3b4151;">Payment Processing</h1>
                <p class="lead text-muted">Your order is being processed. Status: {{ ucfirst($order->status) }}</p>
            @endif
        </div>
    </div>

    <!-- Checkout Progress (Final) -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Customer Info</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Payment Method</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Order Processed</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                     style="width: 40px; height: 40px; background-color: #28a745; color: white;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <small class="text-success fw-bold">Complete</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-md-8">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Order Number</h6>
                            <p class="mb-0 fw-bold" style="color: #328a75;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Date</h6>
                            <p class="mb-0">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Payment Status</h6>
                            @if($order->status === 'paid')
                                <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                            @elseif(in_array($order->status, ['failed', 'cancelled', 'expired', 'rejected']))
                                <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Method</h6>
                            <p class="mb-0">
                                @php
                                    $paymentMethods = [
                                        'card' => 'Credit/Debit Card',
                                        'apple_pay' => 'Apple Pay',
                                        'google_pay' => 'Google Pay',
                                        'paypal' => 'PayPal',
                                        'klarna' => 'Klarna'
                                    ];
                                @endphp
                                {{ $paymentMethods[$order->payment_method] ?? ucfirst($order->payment_method) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-shopping-bag me-2"></i>Order Items ({{ $order->items->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                    <div class="row align-items-center border-bottom pb-3 mb-3">
                        <div class="col-md-2">
                            @if($item->product->image)
                            <img src="{{ $item->product->image }}"
                                 alt="{{ $item->product->name }}"
                                 class="img-fluid rounded"
                                 style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                            <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                {{ Str::limit($item->product->description, 80) }}
                            </p>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="fw-bold">Qty: {{ $item->quantity }}</span>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="fw-bold">€{{ number_format($item->price, 2) }}</div>
                            <div class="text-muted" style="font-size: 0.9rem;">each</div>
                            <div class="fw-bold" style="color: #328a75;">
                                €{{ number_format($item->price * $item->quantity, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Order Totals -->
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>€{{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span class="text-success">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">Total:</h6>
                                <h5 class="mb-0" style="color: #328a75;">€{{ number_format($order->total, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <p class="mb-1"><strong>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong></p>
                            <p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $order->customer->email }}</p>
                            <p class="mb-0"><i class="fas fa-phone me-2 text-muted"></i>{{ $order->customer->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Information</h6>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                This is a digital demo - no physical delivery required.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payments->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-credit-card me-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($order->payments as $payment)
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <strong>Payment #{{ $payment->id }}</strong><br>
                            <span class="text-muted">{{ ucfirst($payment->method) }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                        </div>
                        <div class="col-md-3 text-end">
                            <strong>€{{ number_format($payment->amount, 2) }}</strong>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-clipboard-list me-2"></i>What's Next?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success">Order Confirmed</h6>
                                <small class="text-muted">Your order has been received and payment processed.</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Email Confirmation</h6>
                                <small class="text-muted">Order details have been sent to {{ $order->customer->email }}.</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Demo Complete</h6>
                                <small class="text-muted">This completes the checkout demo flow.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0" style="color: #3b4151;">
                        <i class="fas fa-code me-2"></i>Demo Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3"><strong>Congratulations!</strong> You've successfully completed the checkout demo.</p>
                    <div class="mb-3">
                        <h6>SDK Integration Points:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Product management</li>
                            <li><i class="fas fa-check text-success me-2"></i>Customer creation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Order processing</li>
                            <li><i class="fas fa-check text-success me-2"></i>Payment methods</li>
                            <li><i class="fas fa-check text-success me-2"></i>Webhook handling</li>
                        </ul>
                    </div>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-rocket me-2"></i>
                        Ready to implement? Check out our <a href="{{ route('home') }}" style="color: #328a75;">integration guide</a>.
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="mb-3" style="color: #3b4151;">Continue Exploring</h6>

                    @if(in_array($order->status, ['failed', 'cancelled', 'expired', 'rejected']))
                        <a href="{{ route('shop.products.index') }}" class="btn btn-lg w-100 text-white mb-3"
                           style="background-color: #dc3545; border-color: #dc3545;">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                    @elseif($order->status === 'paid')
                        <a href="{{ route('shop.products.index') }}" class="btn btn-lg w-100 text-white mb-3"
                           style="background-color: #328a75; border-color: #328a75;">
                            <i class="fas fa-shopping-cart me-2"></i>Shop More Products
                        </a>
                    @else
                        <a href="{{ url()->current() }}" class="btn btn-lg w-100 text-white mb-3"
                           style="background-color: #ffc107; border-color: #ffc107;">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Status
                        </a>
                        <div class="alert alert-info small mt-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Payment status is checked automatically every 5 minutes
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>

                    <div class="row mt-3">
                        <div class="col-6">
                            <button class="btn btn-outline-secondary w-100" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100" onclick="shareOrder()">
                                <i class="fas fa-share"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -23px;
    top: 20px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid white;
}

.timeline-content {
    margin-left: 0;
}

@media print {
    .btn, .timeline-marker, .timeline-item:not(:last-child):before {
        display: none !important;
    }
}
</style>

<script>
function shareOrder() {
    if (navigator.share) {
        navigator.share({
            title: 'Order Complete - Applax Demo',
            text: 'I just completed a demo checkout on the Applax payment platform!',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'Order link copied to clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(() => {
            Swal.fire({
                icon: 'info',
                title: 'Share Order',
                text: window.location.href,
                confirmButtonText: 'Close'
            });
        });
    }
}

// Auto-scroll to top on page load
document.addEventListener('DOMContentLoaded', function() {
    window.scrollTo(0, 0);

    // Show animation based on status
    setTimeout(() => {
        const icon = document.querySelector('.fa-check.fa-3x, .fa-times.fa-3x, .fa-clock.fa-3x');
        if (icon) {
            icon.style.transform = 'scale(1.2)';
            icon.style.transition = 'transform 0.3s ease';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 300);
        }
    }, 500);
});
</script>
@endsection
