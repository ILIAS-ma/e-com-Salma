<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'composition', 'care_instructions',
        'price', 'compare_at_price', 'collection', 'gender', 'is_active', 'is_featured',
        'sales_count', 'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function availableSizes(): array
    {
        return $this->variants->pluck('size')->unique()->values()->all();
    }

    public function availableColors(): array
    {
        return $this->variants
            ->unique('color_name')
            ->map(fn (ProductVariant $variant) => [
                'name' => $variant->color_name,
                'code' => $variant->color_code,
            ])
            ->values()
            ->all();
    }

    public function totalStock(): int
    {
        return $this->variants->sum('stock');
    }

    public function isInStock(): bool
    {
        return $this->totalStock() > 0;
    }

    public function isOnSale(): bool
    {
        return $this->compare_at_price !== null && $this->compare_at_price > $this->price;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeGender($query, ?string $gender)
    {
        return $gender ? $query->where('gender', $gender) : $query;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
