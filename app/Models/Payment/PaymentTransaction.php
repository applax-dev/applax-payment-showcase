<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'transaction_id',
        'type',
        'status',
        'amount',
        'currency',
        'gateway_transaction_id',
        'description',
        'reason',
        'gateway_response',
        'metadata',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    protected $attributes = [
        'currency' => 'EUR',
        'status' => 'pending'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transaction_id)) {
                $model->transaction_id = 'txn_' . Str::random(16);
            }
        });
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRefunds($query)
    {
        return $query->whereIn('type', ['refund', 'partial_refund']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRefund(): bool
    {
        return in_array($this->type, ['refund', 'partial_refund']);
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'failed', 'cancelled' => 'danger',
            'processing' => 'info',
            default => 'warning'
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'payment' => 'bi-credit-card',
            'refund', 'partial_refund' => 'bi-arrow-counterclockwise',
            'chargeback' => 'bi-exclamation-triangle',
            'fee' => 'bi-currency-exchange',
            default => 'bi-circle'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->isRefund() ? '-' : '';
        return $sign . '€' . number_format($this->amount, 2);
    }

    public static function createPaymentTransaction(Payment $payment, array $gatewayResponse = []): self
    {
        return self::create([
            'payment_id' => $payment->id,
            'type' => 'payment',
            'status' => 'completed',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'gateway_transaction_id' => $payment->gateway_payment_id,
            'description' => 'Payment processed via ' . $payment->method_display_name,
            'gateway_response' => $gatewayResponse,
            'processed_at' => now()
        ]);
    }

    public static function createRefundTransaction(Payment $payment, float $amount, string $reason = null, array $gatewayResponse = []): self
    {
        $type = ($amount >= $payment->amount) ? 'refund' : 'partial_refund';

        return self::create([
            'payment_id' => $payment->id,
            'type' => $type,
            'status' => 'completed',
            'amount' => $amount,
            'currency' => $payment->currency,
            'gateway_transaction_id' => $gatewayResponse['id'] ?? null,
            'description' => 'Refund processed' . ($reason ? ': ' . $reason : ''),
            'reason' => $reason,
            'gateway_response' => $gatewayResponse,
            'processed_at' => now()
        ]);
    }
}