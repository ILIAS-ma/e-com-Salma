@props(['product', 'largeText' => false])

@php
    $primary = $product->images->first();
    $secondary = $product->images->get(1);
    $productUrl = route('products.show', $product);
@endphp

<div
    data-product-card
    class="product-tile"
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    data-reveal
    :class="{ 'is-visible': shown }"
>
    <a href="{{ $productUrl }}" class="relative block aspect-[2/3] overflow-hidden bg-sand">
        @if ($primary)
            <img
                src="{{ $primary->url }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-cover"
                style="view-transition-name: product-image-{{ $product->id }}"
            >
        @endif

        @if ($secondary)
            <img
                src="{{ $secondary->url }}"
                alt=""
                loading="lazy"
                aria-hidden="true"
                class="tile-img-alt absolute inset-0 h-full w-full object-cover"
            >
        @endif
    </a>

    <div class="mt-2.5 flex items-start justify-between gap-3">
        <a href="{{ $productUrl }}" class="min-w-0">
            <p @class(['truncate uppercase tracking-wide', 'text-[11px]' => ! $largeText, 'text-[12px]' => $largeText])>{{ $product->name }}</p>
            <p @class(['mt-1 flex flex-wrap gap-x-2', 'text-[11px]' => ! $largeText, 'text-[12px]' => $largeText])>
                @if ($product->isOnSale())
                    <span class="text-stone line-through">{{ number_format($product->compare_at_price, 2, ',', ' ') }} MAD</span>
                    <span class="text-brand">{{ number_format($product->price, 2, ',', ' ') }} MAD</span>
                    <span class="text-brand">-{{ round((1 - $product->price / $product->compare_at_price) * 100) }}%</span>
                @else
                    <span>{{ number_format($product->price, 2, ',', ' ') }} MAD</span>
                @endif
            </p>
        </a>
        <button
            type="button"
            data-quick-view
            data-quick-view-url="{{ route('api.products.quick-view', $product) }}"
            data-product-url="{{ $productUrl }}"
            data-product-name="{{ $product->name }}"
            class="-mr-1 shrink-0 p-1 transition-opacity duration-200 hover:opacity-50"
            aria-label="Ajout rapide : {{ $product->name }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="square" d="M12 4v16M4 12h16" />
            </svg>
        </button>
    </div>
</div>
