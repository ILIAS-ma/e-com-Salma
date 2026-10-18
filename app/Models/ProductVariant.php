<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'sku', 'size', 'color_name', 'color_code', 'price_override', 'stock',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function price(): float
    {
        return (float) ($this->price_override ?? $this->product->price);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
