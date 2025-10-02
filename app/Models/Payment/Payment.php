<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Shop\Order;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway_payment_id',
        'status',
        'method',
        'amount',
        'refunded_amount',
        'currency',
        'reference',
        'failure_reason',
        'gateway_response',
        'payment_details',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'payment_details' => 'array',
        'processed_at' => 'datetime'
    ];

    protected $attributes = [
        'currency' => 'EUR',
        'status' => 'pending',
        'refunded_amount' => 0
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class)->orderBy('created_at', 'desc');
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && $this->refunded_amount < $this->amount;
    }

    public function getRemainingRefundableAmountAttribute(): float
    {
        return $this->amount - $this->refunded_amount;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'failed', 'cancelled' => 'danger',
            'processing' => 'info',
            'refunded', 'partially_refunded' => 'warning',
            default => 'secondary'
        };
    }

    public function getMethodDisplayNameAttribute(): string
    {
        return match($this->method) {
            'card' => 'Credit/Debit Card',
            'apple_pay' => 'Apple Pay',
            'google_pay' => 'Google Pay',
            'paypal' => 'PayPal',
            'klarna' => 'Klarna',
            'bank_transfer' => 'Bank Transfer',
            'volt' => 'Volt',
            'zimpler' => 'Zimpler',
            'alipay' => 'AliPay',
            'wechat' => 'WeChat',
            'moto' => 'MOTO',
            default => ucfirst($this->method)
        };
    }

    public function markAsCompleted(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => $gatewayResponse
        ]);

        if ($this->order) {
            $this->order->markAsPaid();
        }
    }

    public function markAsFailed(string $reason, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'processed_at' => now(),
            'gateway_response' => $gatewayResponse
        ]);
    }

    public function addRefund(float $amount): void
    {
        $this->refunded_amount += $amount;

        if ($this->refunded_amount >= $this->amount) {
            $this->status = 'refunded';
        } else {
            $this->status = 'partially_refunded';
        }

        $this->save();
    }
}