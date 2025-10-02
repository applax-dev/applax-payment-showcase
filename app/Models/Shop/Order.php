<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Shop\Customer;
use App\Models\Shop\OrderItem;
use App\Models\Payment\Payment;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_order_id',
        'customer_id',
        'status',
        'total',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'currency',
        'payment_method',
        'notes',
        'gateway_data',
        'payment_urls',
        'expires_at',
        'paid_at'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'gateway_data' => 'array',
        'payment_urls' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    protected $attributes = [
        'currency' => 'EUR',
        'status' => 'draft',
        'tax_amount' => 0,
        'shipping_amount' => 0
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'issued', 'viewed']);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['draft', 'issued', 'viewed']);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['issued', 'viewed', 'overdue']);
    }

    public function getPaymentUrl(): ?string
    {
        if (empty($this->payment_urls) || !is_array($this->payment_urls)) {
            return null;
        }

        return $this->payment_urls['full_page_checkout'] ??
               $this->payment_urls['api_do_url'] ??
               $this->payment_urls['iframe_checkout'] ??
               null;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['issued', 'viewed', 'overdue', 'expired', 'rejected', 'hold', 'received']);
    }

    public function canBeCaptured(): bool
    {
        return $this->status === 'hold';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'paid';
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2) . ' ' . strtoupper($this->currency);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'failed', 'cancelled', 'expired', 'rejected' => 'danger',
            'hold' => 'warning',
            'in_progress' => 'info',
            default => 'secondary'
        };
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $this->customer->updateLastOrderTime();
    }

    public function isSyncedWithGateway(): bool
    {
        return !empty($this->gateway_order_id);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->total = $this->subtotal + $this->tax_amount + $this->shipping_amount;
        $this->save();
    }

    public function validateForGateway(): void
    {
        if (empty($this->customer)) {
            throw new \Exception('Order must have a customer assigned');
        }

        if ($this->items->isEmpty()) {
            throw new \Exception('Order must have at least one item');
        }

        $this->calculateTotals();

        if ($this->total <= 0) {
            throw new \Exception('Order total must be greater than 0. Current total: ' . $this->total . ' ' . $this->currency);
        }

        if (empty($this->currency)) {
            throw new \Exception('Order must have a currency assigned');
        }
    }

    public function toGatewayFormat(): array
    {
        // Ensure total is calculated and positive
        if ($this->total <= 0) {
            $this->calculateTotals();
        }

        if ($this->total <= 0) {
            throw new \Exception('Order total must be a positive number. Current total: ' . $this->total);
        }

        $data = [
            'id' => (string) $this->id, // Required: Order ID for SDK validation
            'number' => (string) $this->id, // Required: Order reference number
            'referrer' => 'Laravel Module v1.0', // Required: Module identification
            'language' => 'en', // Required: Language code
            'currency' => $this->currency,
            'amount' => round((float) $this->total, 2), // Required: Total order amount for SDK validation
            'client' => $this->customer->toGatewayFormat(),
            // Create single consolidated product with total amount (like OpenCart)
            'products' => [
                [
                    'price' => round((float) $this->total, 2), // Ensure proper formatting
                    'title' => 'Order #' . $this->id . ' - ' . $this->items->count() . ' item(s)',
                    'quantity' => 1
                ]
            ],
            // Add redirect URLs
            'success_redirect' => route('shop.checkout.step', ['step' => 'complete']) . '?order=' . $this->id,
            'failure_redirect' => route('shop.checkout.step', ['step' => 'payment']) . '?error=payment_failed&order=' . $this->id,
            'in_progress_redirect' => route('shop.checkout.step', ['step' => 'complete']) . '?order=' . $this->id,
        ];

        // Only add notes if it has a value
        if (!empty($this->notes)) {
            $data['notes'] = $this->notes;
        }

        return $data;
    }
}