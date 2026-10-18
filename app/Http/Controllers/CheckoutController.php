<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\City;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Redirect;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function show()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $cities = City::active()->with('shippingZone')->orderBy('name')->get();

        return view('checkout.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'cities' => $cities,
        ]);
    }

    public function store(CheckoutRequest $request, CheckoutService $checkout)
    {
        try {
            $result = $checkout->process($request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $order = $result['order'];
        $gatewayResult = $result['gateway_result'];

        if (! empty($gatewayResult['redirect_url']) && $order->payment_method !== 'cod') {
            if (! empty($gatewayResult['fields'])) {
                // CMI needs an auto-submitted POST form rather than a simple redirect
                return view('checkout.cmi-redirect', [
                    'gatewayUrl' => $gatewayResult['redirect_url'],
                    'fields' => $gatewayResult['fields'],
                ]);
            }

            return Redirect::away($gatewayResult['redirect_url']);
        }

        return redirect()->route('checkout.success', $order->reference);
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }
}
