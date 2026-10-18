<?php

namespace App\Services;

use App\Models\City;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        protected CartService $cart,
        protected ShippingCalculator $shipping,
    ) {}

    /**
     * @return array{order: Order, gateway_result: array}
     */
    public function process(array $data): array
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new RuntimeException('Votre panier est vide.');
        }

        $city = City::with('shippingZone')->findOrFail($data['city_id']);

        if (! $city->shippingZone) {
            throw new RuntimeException('Aucune zone de livraison n\'est configurée pour cette ville.');
        }

        return DB::transaction(function () use ($data, $items, $city) {
            // lock variant rows to prevent overselling under concurrent checkouts
            $variantIds = $items->pluck('variant.id');
            $lockedVariants = ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($items as $item) {
                $variant = $lockedVariants->get($item['variant']->id);

                if (! $variant || $variant->stock < $item['quantity']) {
                    throw new RuntimeException("Stock insuffisant pour {$item['variant']->product->name} ({$item['variant']->size} / {$item['variant']->color_name}).");
                }
            }

            $subtotal = $items->sum('line_total');
            $shippingCost = $this->shipping->costFor($city, $subtotal, $data['payment_method']);
            $total = round($subtotal + $shippingCost, 2);

            $order = Order::create([
                'user_id' => auth()->id(),
                'city_id' => $city->id,
                'shipping_zone_id' => $city->shipping_zone_id,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'currency' => 'MAD',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'status' => 'en_attente',
            ]);

            foreach ($items as $item) {
                $variant = $lockedVariants->get($item['variant']->id);

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'size' => $variant->size,
                    'color_name' => $variant->color_name,
                    'unit_price' => $variant->price(),
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);

                $variant->decrement('stock', $item['quantity']);
                $variant->product()->increment('sales_count', $item['quantity']);
            }

            $gateway = PaymentGatewayFactory::make($data['payment_method']);
            $result = $gateway->charge($order);

            $this->cart->clear();

            return ['order' => $order, 'gateway_result' => $result];
        });
    }
}
