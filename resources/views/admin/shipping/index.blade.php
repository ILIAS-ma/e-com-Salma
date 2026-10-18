<x-admin-layout title="Zones de livraison">
    <div class="border border-black/10 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/10 text-xs uppercase tracking-widest2 text-ink/40">
                <tr>
                    <th class="p-4">Zone</th>
                    <th>Villes</th>
                    <th>Tarif</th>
                    <th>Gratuit dès</th>
                    <th>Délai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($zones as $zone)
                    <tr>
                        <td class="p-4">{{ $zone->name }}</td>
                        <td>{{ $zone->cities_count }}</td>
                        <td>{{ number_format($zone->rate, 2) }} MAD</td>
                        <td>{{ $zone->free_shipping_threshold ? number_format($zone->free_shipping_threshold, 2).' MAD' : '—' }}</td>
                        <td>{{ $zone->estimated_days_min }}-{{ $zone->estimated_days_max }} jours</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.shipping.edit', $zone) }}" class="text-stone hover:underline">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
