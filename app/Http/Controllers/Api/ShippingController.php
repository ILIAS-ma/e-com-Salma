<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Services\CartService;
use App\Services\ShippingCalculator;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function quote(Request $request, CartService $cart, ShippingCalculator $calculator)
    {
        $data = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'payment_method' => ['nullable', 'in:cmi,stripe,paypal,cod'],
        ]);

        $city = City::with('shippingZone')->findOrFail($data['city_id']);
        $subtotal = $cart->subtotal();
        $shippingCost = $calculator->costFor($city, $subtotal, $data['payment_method'] ?? 'cmi');

        return response()->json([
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => round($subtotal + $shippingCost, 2),
            'zone' => $city->shippingZone?->name,
            'estimated_days' => $city->shippingZone
                ? "{$city->shippingZone->estimated_days_min}-{$city->shippingZone->estimated_days_max} jours"
                : null,
        ]);
    }
}
