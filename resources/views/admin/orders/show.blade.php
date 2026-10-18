<x-admin-layout title="Commande {{ $order->reference }}">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Articles</h2>
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-black/5">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product_name }} ({{ $item->size }} / {{ $item->color_name }})</td>
                                <td class="text-right">{{ $item->quantity }} &times; {{ number_format($item->unit_price, 2) }} MAD</td>
                                <td class="py-2 text-right font-medium">{{ number_format($item->line_total, 2) }} MAD</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 space-y-1 border-t border-black/10 pt-4 text-sm">
                    <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 2) }} MAD</span></div>
                    <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->shipping_cost, 2) }} MAD</span></div>
                    <div class="flex justify-between font-medium"><span>Total</span><span>{{ number_format($order->total, 2) }} MAD</span></div>
                </div>
            </div>

            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Client &amp; livraison</h2>
                <p class="text-sm">{{ $order->full_name }} &middot; {{ $order->email }} &middot; {{ $order->phone }}</p>
                <p class="mt-1 text-sm text-ink/70">{{ $order->address }}, {{ $order->city->name }} {{ $order->postal_code }}</p>
                <p class="mt-1 text-sm text-ink/50">Zone : {{ $order->city->shippingZone->name }}</p>
                @if ($order->notes)
                    <p class="mt-3 text-sm italic text-ink/60">"{{ $order->notes }}"</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Statut de la commande</h2>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full rounded-none border-black/20 text-sm">
                        @foreach (\App\Models\Order::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-ink py-2 text-xs uppercase tracking-widest2 text-paper">Mettre à jour</button>
                </form>
            </div>

            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Paiement</h2>
                <p class="text-sm">Méthode : {{ strtoupper($order->payment_method) }}</p>
                <p class="text-sm">Statut : {{ $order->payment_status }}</p>
            </div>
        </div>
    </div>
</x-admin-layout>
