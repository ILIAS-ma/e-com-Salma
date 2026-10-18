<x-shop-layout :title="$product->name">
    @php
        $variantsPayload = $product->variants->map(fn ($v) => [
            'id' => $v->id,
            'size' => $v->size,
            'color' => $v->color_name,
            'colorCode' => $v->color_code,
            'stock' => $v->stock,
        ]);
    @endphp

    <div
        x-data="{
            variants: {{ Js::from($variantsPayload) }},
            colors: {{ Js::from($product->availableColors()) }},
            sizes: {{ Js::from($product->availableSizes()) }},
            selectedColor: null,
            selectedSize: null,
            get selectedVariant() {
                return this.variants.find(v => v.color === this.selectedColor && v.size === this.selectedSize) ?? null;
            },
            get inStock() {
                return this.selectedVariant && this.selectedVariant.stock > 0;
            },
            sizeAvailable(size) {
                return this.variants.some(v => v.size === size && v.color === this.selectedColor && v.stock > 0);
            },
        }"
        x-init="selectedColor = colors[0]?.name ?? null"
        class="pb-28 lg:pb-0"
    >
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_minmax(0,1.25fr)_1fr]">
            <!-- Left: description & composition (desktop) -->
            <div class="detail-enter order-3 px-4 pt-10 sm:px-6 lg:order-1 lg:sticky lg:top-20 lg:self-start lg:px-10 lg:pt-24">
                <div class="max-w-xs">
                    <p class="whitespace-pre-line text-[12px] leading-relaxed">{{ $product->description }}</p>

                    @if ($product->composition)
                        <details class="faq-item group mt-10 border-t border-line pt-4">
                            <summary class="label flex cursor-pointer list-none items-center justify-between">
                                Composition &amp; entretien
                                <span class="transition-transform duration-300 group-open:rotate-45">+</span>
                            </summary>
                            <div class="faq-answer mt-4 space-y-2 text-[12px] leading-relaxed text-stone">
                                <p>{{ $product->composition }}</p>
                                <p>{{ $product->care_instructions }}</p>
                            </div>
                        </details>
                    @endif

                    <details class="faq-item group border-t border-line pt-4 @unless ($product->composition) mt-10 @else mt-4 @endunless">
                        <summary class="label flex cursor-pointer list-none items-center justify-between">
                            Livraison &amp; retours
                            <span class="transition-transform duration-300 group-open:rotate-45">+</span>
                        </summary>
                        <div class="faq-answer mt-4 space-y-2 text-[12px] leading-relaxed text-stone">
                            <p>Livraison partout au Maroc, paiement à la livraison disponible.</p>
                            <p>Retours sous 14 jours.</p>
                        </div>
                    </details>
                </div>
            </div>

            <!-- Center: photos (vertical stack on desktop, swipe on mobile) -->
            <div class="order-1 lg:order-2">
                <div class="no-scrollbar flex snap-x snap-mandatory overflow-x-auto lg:flex-col lg:gap-px lg:overflow-visible">
                    @forelse ($product->images as $image)
                        <div class="aspect-[2/3] w-full shrink-0 snap-start bg-sand">
                            <img
                                src="{{ $image->url }}"
                                alt="{{ $product->name }}"
                                @unless ($loop->first) loading="lazy" @endunless
                                class="h-full w-full object-cover"
                                @if ($loop->first) style="view-transition-name: product-image-{{ $product->id }}" @endif
                            >
                        </div>
                    @empty
                        <div class="aspect-[2/3] w-full bg-sand"></div>
                    @endforelse
                </div>
                @if ($product->images->count() > 1)
                    <p class="label mt-3 text-center text-stone lg:hidden">{{ $product->images->count() }} photos &middot; faites glisser</p>
                @endif
            </div>

            <!-- Right: name, price, variants, add to cart -->
            <div class="detail-enter order-2 px-4 pt-6 sm:px-6 lg:order-3 lg:sticky lg:top-20 lg:self-start lg:px-10 lg:pt-24">
                <div class="lg:max-w-xs">
                    <h1 class="text-[13px] font-normal uppercase tracking-wide">{{ $product->name }}</h1>
                    <p class="mt-2 flex gap-2 text-[13px]">
                        @if ($product->isOnSale())
                            <span class="text-stone line-through">{{ number_format($product->compare_at_price, 2, ',', ' ') }} MAD</span>
                            <span class="text-brand">{{ number_format($product->price, 2, ',', ' ') }} MAD</span>
                            <span class="text-brand">-{{ round((1 - $product->price / $product->compare_at_price) * 100) }}%</span>
                        @else
                            <span>{{ number_format($product->price, 2, ',', ' ') }} MAD</span>
                        @endif
                    </p>

                    <form action="{{ route('cart.store') }}" method="POST" class="mt-8">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedVariant?.id">

                        <div class="flex items-center gap-3">
                            <template x-for="color in colors" :key="color.name">
                                <button
                                    type="button"
                                    class="color-swatch"
                                    :style="`background-color: ${color.code}`"
                                    :aria-pressed="selectedColor === color.name"
                                    @click="selectedColor = color.name; selectedSize = null"
                                    :title="color.name"
                                ></button>
                            </template>
                            <span class="label ml-1 text-stone" x-text="selectedColor"></span>
                        </div>

                        <ul class="mt-6 border-t border-line">
                            <template x-for="size in sizes" :key="size">
                                <li>
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between border-b border-line py-3 text-left text-[12px] uppercase tracking-wide transition-colors duration-200"
                                        :class="sizeAvailable(size) ? 'hover:bg-sand' : 'cursor-not-allowed text-stone/60'"
                                        :disabled="!sizeAvailable(size)"
                                        @click="selectedSize = size"
                                    >
                                        <span class="pl-1" x-text="size"></span>
                                        <span x-show="selectedSize === size" class="mr-1 h-2 w-2 bg-ink"></span>
                                        <span x-show="!sizeAvailable(size)" class="pr-1 text-[10px] tracking-widest2">Bientôt disponible</span>
                                    </button>
                                </li>
                            </template>
                        </ul>

                        <button type="button" class="label link-underline mt-4 text-stone hover:text-ink" onclick="document.getElementById('size-guide').showModal()">Guide des tailles</button>

                        <button type="submit" class="btn-primary mt-8 hidden w-full lg:flex" :disabled="!inStock">
                            <span x-text="selectedSize ? 'Ajouter au panier' : 'Sélectionnez une taille'"></span>
                        </button>

                        <!-- Sticky mobile add-to-cart bar -->
                        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-paper p-3 lg:hidden">
                            <button type="submit" class="btn-primary w-full" :disabled="!inStock">
                                <span x-text="selectedSize ? 'Ajouter au panier' : 'Sélectionnez une taille'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <dialog id="size-guide" class="w-full max-w-md bg-paper p-8 backdrop:bg-black/20">
            <div class="flex items-center justify-between">
                <h2 class="label">Guide des tailles</h2>
                <button onclick="document.getElementById('size-guide').close()" class="-mr-1 p-1" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="square" d="M5 5l14 14M19 5L5 19" /></svg>
                </button>
            </div>
            <table class="mt-6 w-full text-left text-[12px]">
                <thead><tr class="border-b border-ink text-[10px] uppercase tracking-widest2"><th class="pb-3 font-normal">Taille</th><th class="pb-3 font-normal">Poitrine (cm)</th><th class="pb-3 font-normal">Taille (cm)</th></tr></thead>
                <tbody class="divide-y divide-line">
                    <tr><td class="py-3">XS</td><td>82</td><td>62</td></tr>
                    <tr><td class="py-3">S</td><td>86</td><td>66</td></tr>
                    <tr><td class="py-3">M</td><td>90</td><td>70</td></tr>
                    <tr><td class="py-3">L</td><td>96</td><td>76</td></tr>
                    <tr><td class="py-3">XL</td><td>102</td><td>82</td></tr>
                </tbody>
            </table>
        </dialog>

        @if ($related->isNotEmpty())
            <section class="mt-28 px-4 sm:px-6 lg:px-10">
                <h2 class="section-title">Vous aimerez aussi</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-2 gap-y-10 sm:grid-cols-4 lg:grid-cols-6">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-shop-layout>
