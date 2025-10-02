<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'gateway_event_id',
        'payload',
        'signature',
        'status',
        'error_message',
        'attempts',
        'processed_at',
        'next_retry_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'processed_at' => 'datetime',
        'next_retry_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending',
        'attempts' => 0
    ];

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeReadyForRetry($query)
    {
        return $query->where('status', 'failed')
                    ->where('next_retry_at', '<=', now())
                    ->where('attempts', '<', 5);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed' &&
               $this->attempts < 5 &&
               (!$this->next_retry_at || $this->next_retry_at <= now());
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'processed' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    public function markAsProcessed(): void
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now(),
            'error_message' => null
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->increment('attempts');

        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'next_retry_at' => $this->calculateNextRetryTime()
        ]);
    }

    public function retry(): void
    {
        $this->update([
            'status' => 'pending',
            'next_retry_at' => null
        ]);
    }

    private function calculateNextRetryTime(): \Carbon\Carbon
    {
        // Exponential backoff: 1min, 5min, 15min, 30min, 60min
        $delays = [1, 5, 15, 30, 60];
        $delay = $delays[min($this->attempts - 1, count($delays) - 1)];

        return now()->addMinutes($delay);
    }
}