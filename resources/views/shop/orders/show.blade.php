@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Order #{{ $order->id }}</h2>
                    <div class="text-muted">
                        Created {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                        @if($order->gateway_order_id)
                            • Gateway ID: {{ $order->gateway_order_id }}
                        @endif
                    </div>
                </div>
                <div>
                    <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-lg-8">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Customer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong> {{ $order->customer->email }}
                                </div>
                                <div class="col-md-6 mt-2">
                                    <strong>Phone:</strong> {{ $order->customer->phone }}
                                </div>
                                @if($order->customer->gateway_client_id)
                                    <div class="col-md-6 mt-2">
                                        <strong>Gateway Client ID:</strong>
                                        <code class="small">{{ $order->customer->gateway_client_id }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>
                                Order Items
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $item->product->name }}</div>
                                                    @if($item->product->description)
                                                        <small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>€{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td class="text-end">€{{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end"><strong>€{{ number_format($order->subtotal, 2) }}</strong></td>
                                        </tr>
                                        @if($order->tax_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">Tax:</td>
                                                <td class="text-end">€{{ number_format($order->tax_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($order->shipping_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">Shipping:</td>
                                                <td class="text-end">€{{ number_format($order->shipping_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-success">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>€{{ number_format($order->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($order->payments->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Payment History
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($order->payments as $payment)
                                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div>
                                            <div class="fw-bold">
                                                Payment #{{ $payment->id }}
                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }} ms-2">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                {{ $payment->created_at->format('M j, Y g:i A') }}
                                                @if($payment->gateway_transaction_id)
                                                    • Transaction: {{ $payment->gateway_transaction_id }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">€{{ number_format($payment->amount, 2) }}</div>
                                            <small class="text-muted">{{ ucfirst($payment->payment_method ?? 'Card') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Order Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            @if($order->canBePaid())
                                @if($order->getPaymentUrl())
                                    <a href="{{ $order->getPaymentUrl() }}" class="btn btn-warning w-100 mb-2" target="_blank">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Continue Payment
                                    </a>
                                @else
                                    <a href="{{ route('shop.orders.retry', $order) }}" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-redo me-2"></i>
                                        Retry Payment
                                    </a>
                                @endif
                            @endif

                            <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Orders
                            </a>

                            @if($order->status === 'paid')
                                <button class="btn btn-outline-success w-100 mb-2" disabled>
                                    <i class="fas fa-check me-2"></i>
                                    Payment Completed
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Order Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="fw-bold text-primary">{{ $order->items->count() }}</div>
                                    <small class="text-muted">Items</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-success">€{{ number_format($order->total, 2) }}</div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>

                            <hr>

                            <div class="small">
                                <div class="d-flex justify-content-between">
                                    <span>Payment Method:</span>
                                    <span>{{ ucfirst($order->payment_method ?? 'Not set') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Currency:</span>
                                    <span>{{ strtoupper($order->currency) }}</span>
                                </div>
                                @if($order->expires_at)
                                    <div class="d-flex justify-content-between">
                                        <span>Expires:</span>
                                        <span class="{{ $order->expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                            {{ $order->expires_at->format('M j, g:i A') }}
                                        </span>
                                    </div>
                                @endif
                                @if($order->paid_at)
                                    <div class="d-flex justify-content-between text-success">
                                        <span>Paid:</span>
                                        <span>{{ $order->paid_at->format('M j, g:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Gateway Data -->
                    @if($order->gateway_data || $order->payment_urls)
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-cogs me-2"></i>
                                    Technical Details
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($order->gateway_data)
                                    <div class="mb-3">
                                        <small class="text-muted fw-bold d-block mb-1">Gateway Data:</small>
                                        <pre class="small bg-light p-2 rounded"><code>{{ json_encode($order->gateway_data, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif

                                @if($order->payment_urls)
                                    <div>
                                        <small class="text-muted fw-bold d-block mb-1">Payment URLs:</small>
                                        <pre class="small bg-light p-2 rounded"><code>{{ json_encode($order->payment_urls, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection