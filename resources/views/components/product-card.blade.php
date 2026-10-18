@props(['product', 'largeText' => false])

@php
    $primary = $product->images->first();
@endphp

<a
    href="{{ route('products.show', $product) }}"
    data-quick-view
    data-quick-view-url="{{ route('api.products.quick-view', $product) }}"
    class="product-tile block"
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    data-reveal
    :class="{ 'is-visible': shown }"
>
    <div class="relative aspect-[3/4] overflow-hidden bg-sand">
        @if ($primary)
            <img
                src="{{ $primary->url }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover"
                style="view-transition-name: product-image-{{ $product->id }}"
            >
        @endif

        @if ($product->isOnSale())
            <span class="absolute left-2 top-2 text-[10px] uppercase tracking-widest2 text-ink">Promo</span>
        @endif
    </div>
    <div class="mt-2.5">
        <p @class(['text-ink', 'text-[13px]' => ! $largeText, 'text-sm' => $largeText])>{{ $product->name }}</p>
        <div class="mt-0.5 flex items-center gap-2">
            <span @class(['text-stone', 'text-[13px]' => ! $largeText, 'text-sm' => $largeText])>{{ number_format($product->price, 2) }} MAD</span>
            @if ($product->isOnSale())
                <span @class(['text-stone/60 line-through', 'text-[11px]' => ! $largeText, 'text-xs' => $largeText])>{{ number_format($product->compare_at_price, 2) }} MAD</span>
            @endif
        </div>
    </div>
</a>
