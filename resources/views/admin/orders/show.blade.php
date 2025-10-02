@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #328a75;">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" style="color: #328a75;">Orders</a></li>
                    <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="color: #3b4151;">
                <i class="bi bi-receipt me-2"></i>Order #{{ $order->id }}
            </h1>
        </div>
        <div>
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'secondary',
                    'issued' => 'info'
                ];
            @endphp
            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} fs-6 me-2">
                {{ ucfirst($order->status) }}
            </span>
            @if($order->gateway_order_id)
                <button onclick="syncOrder()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i>Sync Status
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}"
                                                         class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if($item->product_description)
                                                        <br><small class="text-muted">{{ Str::limit($item->product_description, 100) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>€{{ number_format($item->unit_price, 2) }}</td>
                                        <td><strong>€{{ number_format($item->total_price, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>€{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Payment History</h6>
                </div>
                <div class="card-body">
                    @if($order->payments->count() > 0)
                        @foreach($order->payments as $payment)
                            <!-- Payment Summary -->
                            <div class="border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>{{ $payment->method_display_name }}</strong><br>
                                        <small class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-2">
                                        @php
                                            $paymentStatusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'cancelled' => 'secondary',
                                                'refunded' => 'info',
                                                'partially_refunded' => 'info'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $paymentStatusColors[$payment->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>€{{ number_format($payment->amount, 2) }}</strong>
                                        @if($payment->refunded_amount > 0)
                                            <br><small class="text-muted">Refunded: €{{ number_format($payment->refunded_amount, 2) }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-5 text-end">
                                        @if($payment->status === 'completed' && $payment->refunded_amount < $payment->amount)
                                            <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->amount - $payment->refunded_amount }})"
                                                    class="btn btn-sm btn-outline-warning me-1">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Refund
                                            </button>
                                        @endif
                                        @if($payment->gateway_payment_id)
                                            <small class="text-muted">ID: {{ Str::limit($payment->gateway_payment_id, 12) }}</small>
                                        @endif
                                    </div>
                                </div>
                                @if($payment->failure_reason)
                                    <div class="mt-2 text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $payment->failure_reason }}
                                    </div>
                                @endif

                                <!-- Transaction History -->
                                @if($payment->transactions->count() > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-clock-history me-1"></i>Transaction History
                                        </h6>
                                        <div class="timeline">
                                            @foreach($payment->transactions as $transaction)
                                                <div class="timeline-item d-flex mb-3">
                                                    <div class="timeline-marker me-3">
                                                        <div class="bg-{{ $transaction->status_color }} rounded-circle d-flex align-items-center justify-content-center"
                                                             style="width: 32px; height: 32px; color: white; font-size: 12px;">
                                                            <i class="{{ $transaction->type_icon }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-content flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</strong>
                                                                <span class="badge bg-{{ $transaction->status_color }} badge-sm ms-2">
                                                                    {{ ucfirst($transaction->status) }}
                                                                </span>
                                                                @if($transaction->description)
                                                                    <br><small class="text-muted">{{ $transaction->description }}</small>
                                                                @endif
                                                                @if($transaction->reason)
                                                                    <br><small class="text-warning"><i class="bi bi-info-circle me-1"></i>{{ $transaction->reason }}</small>
                                                                @endif
                                                            </div>
                                                            <div class="text-end">
                                                                <strong class="fw-bold {{ $transaction->isRefund() ? 'text-warning' : 'text-success' }}">
                                                                    {{ $transaction->formatted_amount }}
                                                                </strong>
                                                                <br><small class="text-muted">{{ $transaction->processed_at ? $transaction->processed_at->format('M d, H:i') : $transaction->created_at->format('M d, H:i') }}</small>
                                                                @if($transaction->gateway_transaction_id)
                                                                    <br><small class="text-muted">ID: {{ Str::limit($transaction->gateway_transaction_id, 10) }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No payment records found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Gateway Data -->
            @if($gatewayOrderData)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Gateway Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Gateway Status:</strong> {{ $gatewayOrderData['status'] ?? 'Unknown' }}<br>
                                <strong>Gateway ID:</strong> <code>{{ $order->gateway_order_id }}</code><br>
                                @if(isset($gatewayOrderData['created_at']))
                                    <strong>Created:</strong> {{ \Carbon\Carbon::parse($gatewayOrderData['created_at'])->format('M d, Y H:i') }}<br>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if(isset($gatewayOrderData['amount']))
                                    <strong>Gateway Amount:</strong> €{{ number_format($gatewayOrderData['amount'] / 100, 2) }}<br>
                                @endif
                                @if(isset($gatewayOrderData['currency']))
                                    <strong>Currency:</strong> {{ $gatewayOrderData['currency'] }}<br>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Summary & Actions -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ \App\Helpers\GdprHelper::maskName($order->customer->first_name, $order->customer->last_name) }}</strong><br>
                        <i class="bi bi-envelope me-1"></i>{{ \App\Helpers\GdprHelper::maskEmail($order->customer->email) }}<br>
                        <i class="bi bi-phone me-1"></i>{{ \App\Helpers\GdprHelper::maskPhone($order->customer->phone) }}
                    </div>
                    @if($order->customer->gateway_client_id)
                        <div class="small">
                            <strong>Gateway Client ID:</strong><br>
                            <code>{{ $order->customer->gateway_client_id }}</code>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>€{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span>€{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span>€{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>€{{ number_format($order->total, 2) }}</strong>
                    </div>
                    <div class="mt-3 small text-muted">
                        <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
                        <strong>Currency:</strong> {{ $order->currency }}<br>
                        <strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($order->status, ['issued', 'pending']))
                            <button onclick="capturePayment()" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Capture Payment
                            </button>
                        @endif

                        @if(in_array($order->status, ['paid']))
                            @php
                                $totalRefunded = $order->payments->sum('refunded_amount');
                                $remainingRefundable = $order->total - $totalRefunded;
                            @endphp
                            @if($remainingRefundable > 0)
                                <button onclick="showRefundModal({{ $order->id }}, {{ $remainingRefundable }})" class="btn btn-warning">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Process Refund
                                    <small class="d-block">€{{ number_format($remainingRefundable, 2) }} available</small>
                                </button>
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-check-circle me-1"></i>Order fully refunded
                                </div>
                            @endif
                        @endif

                        @if(in_array($order->status, ['pending', 'issued']))
                            <button onclick="cancelOrder()" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i>Cancel Order
                            </button>
                        @endif

                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-person me-1"></i>View Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="refundForm">
                    <div class="mb-3">
                        <label class="form-label">Refund Amount (€)</label>
                        <input type="number" step="0.01" class="form-control" id="refundAmount" required>
                        <div class="form-text">Maximum refundable amount: €<span id="maxRefund">0.00</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea class="form-control" id="refundReason" rows="3" placeholder="Refund reason..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="processRefund()" class="btn btn-warning">Process Refund</button>
            </div>
        </div>
    </div>
</div>

<script>
function syncOrder() {
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;

    button.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i>Syncing...';
    button.disabled = true;

    fetch(`/admin/orders/{{ $order->id }}/sync`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Synced',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Sync Failed',
                text: error.message
            });
        })
        .finally(() => {
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
}

function capturePayment() {
    Swal.fire({
        title: 'Capture Payment',
        text: 'Are you sure you want to capture the payment for this order?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Capture Payment'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/orders/{{ $order->id }}/capture`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Captured',
                        text: data.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Capture Failed',
                    text: error.message
                });
            });
        }
    });
}

function showRefundModal(orderId, maxAmount) {
    document.getElementById('refundAmount').value = maxAmount.toFixed(2);
    document.getElementById('refundAmount').max = maxAmount;
    document.getElementById('maxRefund').textContent = maxAmount.toFixed(2);
    new bootstrap.Modal(document.getElementById('refundModal')).show();
}

function processRefund() {
    const amount = document.getElementById('refundAmount').value;
    const reason = document.getElementById('refundReason').value;

    fetch(`/admin/orders/{{ $order->id }}/refund`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: amount,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Refund Processed',
                text: `Refunded €${amount}`
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Refund Failed',
            text: error.message
        });
    });
}

function cancelOrder() {
    Swal.fire({
        title: 'Cancel Order',
        text: 'Are you sure you want to cancel this order? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Cancel Order'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/orders/{{ $order->id }}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Cancelled',
                        text: data.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Cancellation Failed',
                    text: error.message
                });
            });
        }
    });
}
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.timeline {
    position: relative;
}

.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    top: 32px;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 20px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: relative;
    z-index: 1;
}

.badge-sm {
    font-size: 0.7em;
}
</style>
@endsection