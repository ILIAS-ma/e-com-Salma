<x-shop-layout title="Panier">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="section-title">Votre panier</h1>

        @if ($items->isEmpty())
            <div class="py-20 text-center">
                <p class="text-stone">Votre panier est vide.</p>
                <a href="{{ route('shop.index') }}" class="btn-primary mt-6">Continuer mes achats</a>
            </div>
        @else
            <div class="mt-10 divide-y divide-line border-y border-line">
                @foreach ($items as $item)
                    @php $variant = $item['variant']; @endphp
                    <div class="flex gap-4 py-6">
                        <div class="h-32 w-24 shrink-0 overflow-hidden rounded-none bg-sand">
                            @if ($image = $variant->product->images->first())
                                <img src="{{ $image->url }}" alt="{{ $variant->product->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col justify-between">
                            <div>
                                <p class="text-sm font-medium">{{ $variant->product->name }}</p>
                                <p class="mt-1 text-xs text-stone">{{ $variant->color_name }} &middot; Taille {{ $variant->size }}</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <form action="{{ route('cart.update', $variant->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <select name="quantity" onchange="this.form.submit()" class="rounded-none border-line text-sm">
                                        @for ($i = 1; $i <= min(10, $variant->stock); $i++)
                                            <option value="{{ $i }}" @selected($item['quantity'] === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>
                                <p class="text-sm font-medium">{{ number_format($item['line_total'], 2) }} MAD</p>
                            </div>
                        </div>
                        <form action="{{ route('cart.destroy', $variant->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-stone/60 hover:text-stone" aria-label="Retirer">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between">
                <a href="{{ route('shop.index') }}" class="link-underline text-sm font-semibold">Continuer mes achats</a>
                <div class="text-right">
                    <p class="text-sm text-stone">Sous-total</p>
                    <p class="text-xl font-semibold">{{ number_format($subtotal, 2, ',', ' ') }} MAD</p>
                </div>
            </div>

            <a href="{{ route('checkout.show') }}" class="btn-primary mt-8 w-full py-4">
                Passer à la commande
            </a>
        @endif
    </div>
</x-shop-layout>
