<x-admin-layout title="Modifier : {{ $zone->name }}">
    <form action="{{ route('admin.shipping.update', $zone) }}" method="POST" class="max-w-xl space-y-4 border border-black/10 bg-white p-6">
        @csrf @method('PATCH')

        <label class="block text-sm">
            Nom de la zone
            <input name="name" value="{{ old('name', $zone->name) }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
        </label>

        <label class="block text-sm">
            Description
            <textarea name="description" rows="2" class="mt-1 w-full rounded-none border-black/20 text-sm">{{ old('description', $zone->description) }}</textarea>
        </label>

        <div class="grid grid-cols-2 gap-4">
            <label class="block text-sm">
                Tarif standard (MAD)
                <input type="number" step="0.01" name="rate" value="{{ old('rate', $zone->rate) }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
            </label>
            <label class="block text-sm">
                Surtaxe COD (MAD)
                <input type="number" step="0.01" name="cod_surcharge" value="{{ old('cod_surcharge', $zone->cod_surcharge) }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
            </label>
        </div>

        <label class="block text-sm">
            Livraison gratuite dès (MAD, laisser vide pour désactiver)
            <input type="number" step="0.01" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $zone->free_shipping_threshold) }}" class="mt-1 w-full rounded-none border-black/20 text-sm">
        </label>

        <div class="grid grid-cols-2 gap-4">
            <label class="block text-sm">
                Délai min (jours)
                <input type="number" name="estimated_days_min" value="{{ old('estimated_days_min', $zone->estimated_days_min) }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
            </label>
            <label class="block text-sm">
                Délai max (jours)
                <input type="number" name="estimated_days_max" value="{{ old('estimated_days_max', $zone->estimated_days_max) }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
            </label>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone->is_active))>
            Zone active
        </label>

        <div class="pt-2">
            <p class="mb-2 text-xs uppercase tracking-widest2 text-ink/40">Villes ({{ $zone->cities->count() }})</p>
            <p class="text-sm text-ink/60">{{ $zone->cities->pluck('name')->join(', ') }}</p>
        </div>

        <button class="bg-ink px-6 py-2 text-xs uppercase tracking-widest2 text-paper">Enregistrer</button>
    </form>
</x-admin-layout>
