<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name', 'description', 'rate', 'cod_surcharge', 'free_shipping_threshold',
        'estimated_days_min', 'estimated_days_max', 'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'cod_surcharge' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Cost of shipping this zone for a given cart subtotal and payment method.
     */
    public function costFor(float $subtotal, string $paymentMethod = 'cmi'): float
    {
        if ($this->free_shipping_threshold !== null && $subtotal >= (float) $this->free_shipping_threshold) {
            $cost = 0.0;
        } else {
            $cost = (float) $this->rate;
        }

        if ($paymentMethod === 'cod') {
            $cost += (float) $this->cod_surcharge;
        }

        return $cost;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
