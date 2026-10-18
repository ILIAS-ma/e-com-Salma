<x-shop-layout title="Commande confirmée">
    <div class="mx-auto max-w-2xl px-4 py-20 text-center sm:px-6 lg:px-8">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-ink text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>
        <h1 class="mt-6 text-lg uppercase tracking-widest2">Merci, {{ $order->full_name }} !</h1>
        <p class="mt-2 text-stone">Votre commande <strong>{{ $order->reference }}</strong> a bien été enregistrée.</p>

        <div class="mt-10 space-y-2 border border-line p-6 text-left text-sm">
            <div class="flex justify-between"><span>Statut</span><span>{{ $order->statusLabel() }}</span></div>
            <div class="flex justify-between"><span>Mode de paiement</span><span>{{ strtoupper($order->payment_method) }}</span></div>
            <div class="flex justify-between font-medium"><span>Total</span><span>{{ number_format($order->total, 2) }} MAD</span></div>
        </div>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('orders.invoice', $order) }}" class="border border-ink px-8 py-3 text-xs uppercase tracking-widest2">Télécharger la facture</a>
            <a href="{{ route('shop.index') }}" class="bg-ink px-8 py-3 text-xs uppercase tracking-widest2 text-paper">Continuer mes achats</a>
        </div>
    </div>
</x-shop-layout>
