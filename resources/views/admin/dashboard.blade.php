<x-admin-layout title="Tableau de bord">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="border border-black/10 bg-white p-6">
            <p class="text-xs uppercase tracking-widest2 text-ink/50">Revenu du mois</p>
            <p class="mt-2 text-2xl">{{ number_format($stats['revenue_month'], 2) }} MAD</p>
        </div>
        <div class="border border-black/10 bg-white p-6">
            <p class="text-xs uppercase tracking-widest2 text-ink/50">Commandes en attente</p>
            <p class="mt-2 text-2xl">{{ $stats['orders_pending'] }}</p>
        </div>
        <div class="border border-black/10 bg-white p-6">
            <p class="text-xs uppercase tracking-widest2 text-ink/50">Total commandes</p>
            <p class="mt-2 text-2xl">{{ $stats['orders_total'] }}</p>
        </div>
        <div class="border border-black/10 bg-white p-6">
            <p class="text-xs uppercase tracking-widest2 text-ink/50">Stock faible (&le;5)</p>
            <p class="mt-2 text-2xl text-stone">{{ $stats['low_stock'] }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="border border-black/10 bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-sm font-medium">Commandes récentes</h2>
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-widest2 text-ink/40">
                    <tr><th class="pb-2">Référence</th><th>Ville</th><th>Statut</th><th class="text-right">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td class="py-2"><a href="{{ route('admin.orders.show', $order) }}" class="hover:text-stone">{{ $order->reference }}</a></td>
                            <td>{{ $order->city->name }}</td>
                            <td>{{ $order->statusLabel() }}</td>
                            <td class="text-right">{{ number_format($order->total, 2) }} MAD</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border border-black/10 bg-white p-6">
            <h2 class="mb-4 text-sm font-medium">Commandes par statut</h2>
            <ul class="space-y-2 text-sm">
                @foreach (\App\Models\Order::STATUSES as $key => $label)
                    <li class="flex justify-between">
                        <span>{{ $label }}</span>
                        <span class="font-medium">{{ $salesByStatus[$key] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-admin-layout>
