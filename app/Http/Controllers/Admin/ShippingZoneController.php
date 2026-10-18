<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::withCount('cities')->orderBy('name')->get();

        return view('admin.shipping.index', compact('zones'));
    }

    public function edit(ShippingZone $shippingZone)
    {
        $shippingZone->load('cities');

        return view('admin.shipping.edit', ['zone' => $shippingZone]);
    }

    public function update(Request $request, ShippingZone $shippingZone)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rate' => ['required', 'numeric', 'min:0'],
            'cod_surcharge' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'estimated_days_min' => ['required', 'integer', 'min:0'],
            'estimated_days_max' => ['required', 'integer', 'gte:estimated_days_min'],
            'is_active' => ['boolean'],
        ]);

        $shippingZone->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Zone de livraison mise à jour.');
    }
}
