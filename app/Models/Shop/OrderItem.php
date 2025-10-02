<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shop\Order;
use App\Models\Shop\Product;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_name',
        'product_description',
        'product_snapshot'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price, 2);
    }

    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
    }

    protected static function booted()
    {
        static::saving(function (OrderItem $item) {
            $item->calculateTotalPrice();
        });

        static::saved(function (OrderItem $item) {
            $item->order->calculateTotals();
        });

        static::deleted(function (OrderItem $item) {
            $item->order->calculateTotals();
        });
    }
}