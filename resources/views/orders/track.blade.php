<x-shop-layout title="Suivi de commande">
    <div class="mx-auto max-w-xl px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-lg uppercase tracking-widest2">Suivre ma commande</h1>
        <p class="mt-2 text-sm text-stone">Renseignez votre numéro de commande et votre email pour suivre son statut.</p>

        <form method="GET" class="mt-8 space-y-4">
            <input name="reference" value="{{ request('reference') }}" placeholder="Référence (ex: ATL-260919-AB12C)" required class="w-full rounded-none border-line text-sm">
            <input type="email" name="email" value="{{ request('email') }}" placeholder="Email utilisé lors de la commande" required class="w-full rounded-none border-line text-sm">
            <button type="submit" class="btn-primary w-full">Suivre ma commande</button>
        </form>

        @if ($order)
            @php
                $steps = ['en_attente', 'confirmee', 'en_preparation', 'en_livraison', 'livree'];
                $currentIndex = array_search($order->status, $steps);
            @endphp

            <div class="mt-12 border border-line p-6">
                <div class="flex items-center justify-between">
                    <p class="font-medium">{{ $order->reference }}</p>
                    <p class="text-sm text-stone">{{ number_format($order->total, 2) }} MAD</p>
                </div>

                @if ($order->status === 'annulee')
                    <p class="mt-4 text-sm text-stone">Cette commande a été annulée.</p>
                @else
                    <ol class="mt-8 flex justify-between text-xs">
                        @foreach (\App\Models\Order::STATUSES as $key => $label)
                            @continue($key === 'annulee')
                            @php $stepIndex = array_search($key, $steps); @endphp
                            <li class="flex flex-1 flex-col items-center text-center">
                                <span class="mb-2 h-3 w-3 rounded-full {{ $stepIndex <= $currentIndex ? 'bg-ink' : 'bg-black/10' }}"></span>
                                <span class="{{ $stepIndex <= $currentIndex ? 'text-ink' : 'text-ink/30' }}">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                <div class="mt-8 divide-y divide-line border-t border-line text-sm">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between py-3">
                            <span>{{ $item->product_name }} ({{ $item->color_name }} / {{ $item->size }}) &times; {{ $item->quantity }}</span>
                            <span>{{ number_format($item->line_total, 2) }} MAD</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>
