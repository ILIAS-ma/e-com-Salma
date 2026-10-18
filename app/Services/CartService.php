<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * A lightweight session-based cart. No persistence is needed for an MVP:
 * the cart lives entirely in the session and is converted into order_items
 * only once the customer completes checkout (see CheckoutService).
 */
class CartService
{
    protected const SESSION_KEY = 'cart';

    public function add(ProductVariant $variant, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$variant->id] = ($cart[$variant->id] ?? 0) + $quantity;
        $this->save($cart);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $quantity;
        }

        $this->save($cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return empty($this->raw());
    }

    /**
     * @return Collection<int, array{variant: ProductVariant, quantity: int, line_total: float}>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        $variants = ProductVariant::with(['product.images'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function (int $quantity, int $variantId) use ($variants) {
                $variant = $variants->get($variantId);

                if (! $variant) {
                    return null;
                }

                $quantity = min($quantity, max($variant->stock, 0));

                return [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'line_total' => round($variant->price() * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return round($this->items()->sum('line_total'), 2);
    }

    protected function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    protected function save(array $cart): void
    {
        Session::put(self::SESSION_KEY, $cart);
    }
}
