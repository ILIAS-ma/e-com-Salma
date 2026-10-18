<x-admin-layout title="Commandes">
    <form method="GET" class="mb-6 flex gap-3">
        <input name="search" value="{{ request('search') }}" placeholder="Référence ou email..." class="w-64 rounded-none border-black/20 text-sm">
        <select name="status" onchange="this.form.submit()" class="rounded-none border-black/20 text-sm">
            <option value="">Tous les statuts</option>
            @foreach (\App\Models\Order::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="border border-ink px-4 py-2 text-xs uppercase tracking-widest2">Filtrer</button>
    </form>

    <div class="border border-black/10 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/10 text-xs uppercase tracking-widest2 text-ink/40">
                <tr>
                    <th class="p-4">Référence</th>
                    <th>Client</th>
                    <th>Ville</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($orders as $order)
                    <tr>
                        <td class="p-4"><a href="{{ route('admin.orders.show', $order) }}" class="hover:text-stone">{{ $order->reference }}</a></td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->city->name }}</td>
                        <td>{{ strtoupper($order->payment_method) }}</td>
                        <td>{{ $order->statusLabel() }}</td>
                        <td class="p-4 text-right">{{ number_format($order->total, 2) }} MAD</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-admin-layout>
