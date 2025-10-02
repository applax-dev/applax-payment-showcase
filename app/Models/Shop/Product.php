<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Shop\OrderItem;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'image',
        'gateway_product_id',
        'status',
        'gateway_data'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'gateway_data' => 'array',
        'status' => 'string'
    ];

    protected $attributes = [
        'currency' => 'EUR',
        'status' => 'active'
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . strtoupper($this->currency);
    }

    public function isSyncedWithGateway(): bool
    {
        return !empty($this->gateway_product_id);
    }

    public function toGatewayFormat(): array
    {
        return [
            'title' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency' => $this->currency,
        ];
    }
}