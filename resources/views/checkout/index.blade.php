<x-shop-layout title="Commande">
    <div
        x-data="{
            cityId: '{{ old('city_id') }}',
            paymentMethod: '{{ old('payment_method', 'cod') }}',
            subtotal: {{ $subtotal }},
            shippingCost: 0,
            total: {{ $subtotal }},
            zoneName: null,
            estimatedDays: null,
            loading: false,
            async refreshQuote() {
                if (!this.cityId) return;
                this.loading = true;
                try {
                    const params = new URLSearchParams({ city_id: this.cityId, payment_method: this.paymentMethod });
                    const res = await fetch(`{{ route('api.shipping.quote') }}?${params}`);
                    const data = await res.json();
                    this.shippingCost = data.shipping_cost;
                    this.total = data.total;
                    this.zoneName = data.zone;
                    this.estimatedDays = data.estimated_days;
                } finally {
                    this.loading = false;
                }
            },
        }"
        x-init="refreshQuote()"
        class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8"
    >
        <h1 class="text-lg uppercase tracking-widest2">Finaliser la commande</h1>

        @if ($errors->any())
            <div class="mt-6 border border-ink bg-sand p-4 text-sm text-ink">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-[1fr_380px]">
            @csrf
            <div class="space-y-8">
                <section>
                    <h2 class="mb-4 text-xs uppercase tracking-widest2 text-stone">Coordonnées</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <input name="full_name" value="{{ old('full_name') }}" placeholder="Nom complet" required class="col-span-2 rounded-none border-line text-sm sm:col-span-1">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required class="rounded-none border-line text-sm">
                        <input name="phone" value="{{ old('phone') }}" placeholder="Téléphone" required class="rounded-none border-line text-sm">
                    </div>
                </section>

                <section>
                    <h2 class="mb-4 text-xs uppercase tracking-widest2 text-stone">Livraison</h2>
                    <div class="space-y-4">
                        <textarea name="address" placeholder="Adresse complète" required rows="2" class="w-full rounded-none border-line text-sm">{{ old('address') }}</textarea>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <select name="city_id" required x-model="cityId" @change="refreshQuote()" class="rounded-none border-line text-sm">
                                <option value="">Sélectionnez une ville</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <input name="postal_code" value="{{ old('postal_code') }}" placeholder="Code postal (optionnel)" class="rounded-none border-line text-sm">
                        </div>
                        <p x-show="zoneName" x-cloak class="text-xs text-ink">
                            Zone : <span x-text="zoneName"></span> &middot; Livraison estimée : <span x-text="estimatedDays"></span>
                        </p>
                        <textarea name="notes" placeholder="Notes de livraison (optionnel)" rows="2" class="w-full rounded-none border-line text-sm">{{ old('notes') }}</textarea>
                    </div>
                </section>

                <section>
                    <h2 class="mb-4 text-xs uppercase tracking-widest2 text-stone">Paiement</h2>
                    <div class="space-y-3">
                        @foreach ([
                            'cod' => ['Paiement à la livraison', 'Payez en espèces à la réception de votre colis.'],
                            'cmi' => ['Carte bancaire (CMI)', 'Paiement sécurisé par carte marocaine ou internationale.'],
                            'stripe' => ['Stripe', 'Paiement par carte via Stripe.'],
                            'paypal' => ['PayPal', 'Paiement via votre compte PayPal.'],
                        ] as $value => [$label, $desc])
                            <label class="flex cursor-pointer items-start gap-3 border p-4" :class="paymentMethod === '{{ $value }}' ? 'border-ink' : 'border-line'">
                                <input type="radio" name="payment_method" value="{{ $value }}" x-model="paymentMethod" @change="refreshQuote()" class="mt-1">
                                <span>
                                    <span class="block text-sm font-medium">{{ $label }}</span>
                                    <span class="block text-xs text-stone">{{ $desc }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            </div>

            <!-- Order summary -->
            <aside class="h-fit space-y-4 border border-line p-6">
                <h2 class="text-sm uppercase tracking-widest2">Résumé</h2>
                <div class="space-y-2 divide-y divide-line text-sm">
                    @foreach ($items as $item)
                        <div class="flex justify-between py-2">
                            <span class="text-stone">{{ $item['variant']->product->name }} &times; {{ $item['quantity'] }}</span>
                            <span>{{ number_format($item['line_total'], 2) }} MAD</span>
                        </div>
                    @endforeach
                </div>
                <div class="space-y-1 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between"><span>Sous-total</span><span x-text="subtotal.toFixed(2) + ' MAD'"></span></div>
                    <div class="flex justify-between">
                        <span>Livraison</span>
                        <span x-text="shippingCost === 0 && cityId ? 'Gratuite' : shippingCost.toFixed(2) + ' MAD'"></span>
                    </div>
                    <div class="flex justify-between font-medium text-base pt-2">
                        <span>Total</span><span x-text="total.toFixed(2) + ' MAD'"></span>
                    </div>
                </div>
                <button type="submit" class="w-full bg-ink py-4 text-xs uppercase tracking-widest2 text-paper hover:opacity-90">
                    Confirmer la commande
                </button>
            </aside>
        </form>
    </div>
</x-shop-layout>
