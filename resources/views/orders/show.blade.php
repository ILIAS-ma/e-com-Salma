<x-shop-layout :title="$order->reference">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-base uppercase tracking-widest2">{{ $order->reference }}</h1>
            <a href="{{ route('orders.invoice', $order) }}" class="text-xs uppercase tracking-widest2 text-stone underline">Télécharger la facture</a>
        </div>

        <p class="mt-2 text-sm text-stone">Statut : {{ $order->statusLabel() }} &middot; Livraison à {{ $order->city->name }}</p>

        <div class="mt-8 divide-y divide-line border-y border-line text-sm">
            @foreach ($order->items as $item)
                <div class="flex justify-between py-3">
                    <span>{{ $item->product_name }} ({{ $item->color_name }} / {{ $item->size }}) &times; {{ $item->quantity }}</span>
                    <span>{{ number_format($item->line_total, 2) }} MAD</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 2) }} MAD</span></div>
            <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->shipping_cost, 2) }} MAD</span></div>
            <div class="flex justify-between font-medium"><span>Total</span><span>{{ number_format($order->total, 2) }} MAD</span></div>
        </div>
    </div>
</x-shop-layout>
