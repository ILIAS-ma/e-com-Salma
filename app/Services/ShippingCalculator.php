<?php

namespace App\Services;

use App\Models\City;

class ShippingCalculator
{
    public function costFor(City $city, float $subtotal, string $paymentMethod = 'cmi'): float
    {
        $zone = $city->shippingZone;

        if (! $zone) {
            return 0.0;
        }

        return $zone->costFor($subtotal, $paymentMethod);
    }
}
