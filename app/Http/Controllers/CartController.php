<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $variant = ProductVariant::findOrFail($data['variant_id']);

        if (! $variant->isInStock()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Cette variante est en rupture de stock.'], 422);
            }

            return back()->with('error', 'Cette variante est en rupture de stock.');
        }

        $this->cart->add($variant, $data['quantity'] ?? 1);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Article ajouté au panier.',
                'cart_count' => $this->cart->count(),
            ]);
        }

        return back()->with('success', 'Article ajouté au panier.');
    }

    public function update(Request $request, int $variantId)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:20']]);
        $this->cart->update($variantId, $data['quantity']);

        return back();
    }

    public function destroy(int $variantId)
    {
        $this->cart->remove($variantId);

        return back()->with('success', 'Article retiré du panier.');
    }
}
