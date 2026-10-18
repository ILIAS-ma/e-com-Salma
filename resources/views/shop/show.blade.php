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
        class="mx-auto max-w-[1600px] px-4 py-8 pb-32 sm:px-6 lg:pb-10"
    >
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.4fr_1fr] lg:gap-16">
            <!-- Lookbook gallery -->
            <div class="grid grid-cols-2 gap-2">
                @forelse ($product->images as $image)
                    <div class="{{ $loop->first ? 'col-span-2' : '' }} aspect-[4/5] overflow-hidden bg-sand">
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                            @if ($loop->first) style="view-transition-name: product-image-{{ $product->id }}" @endif
                        >
                    </div>
                @empty
                    <div class="col-span-2 aspect-[4/5] bg-sand"></div>
                @endforelse
            </div>

            <!-- Details -->
            <div class="detail-enter lg:sticky lg:top-24 lg:self-start">
                <p class="text-[11px] uppercase tracking-widest2 text-stone">{{ $product->collection }}</p>
                <h1 class="mt-2 text-base font-normal">{{ $product->name }}</h1>
                <div class="mt-2 flex items-center gap-3">
                    <span class="text-sm text-ink">{{ number_format($product->price, 2) }} MAD</span>
                    @if ($product->isOnSale())
                        <span class="text-sm text-stone/60 line-through">{{ number_format($product->compare_at_price, 2) }} MAD</span>
                    @endif
                </div>

                <p class="mt-6 text-xs leading-relaxed text-stone">{{ $product->description }}</p>

                <form action="{{ route('cart.store') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariant?.id">

                    <!-- Color picker -->
                    <div>
                        <p class="mb-3 text-[11px] uppercase tracking-widest2 text-stone">
                            Couleur : <span x-text="selectedColor"></span>
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <template x-for="color in colors" :key="color.name">
                                <button
                                    type="button"
                                    class="color-swatch"
                                    :style="`background-color: ${color.code}`"
                                    :aria-pressed="selectedColor === color.name"
                                    @click="selectedColor = color.name"
                                    :title="color.name"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- Size picker -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[11px] uppercase tracking-widest2 text-stone">Taille</p>
                            <button type="button" class="text-xs uppercase tracking-widest2 text-stone underline" onclick="document.getElementById('size-guide').showModal()">Guide des tailles</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="size in sizes" :key="size">
                                <button
                                    type="button"
                                    class="h-11 w-11 border text-sm transition"
                                    :class="{
                                        'border-ink bg-ink text-paper': selectedSize === size,
                                        'border-line hover:border-ink': selectedSize !== size && sizeAvailable(size),
                                        'border-line text-stone/40 line-through': !sizeAvailable(size),
                                    }"
                                    :disabled="!sizeAvailable(size)"
                                    @click="selectedSize = size"
                                    x-text="size"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <p x-show="selectedSize && !inStock" x-cloak class="text-sm text-stone">Rupture de stock pour cette combinaison.</p>

                    <button
                        type="submit"
                        class="hidden w-full bg-ink py-4 text-xs uppercase tracking-widest2 text-paper transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40 lg:block"
                        :disabled="!inStock"
                    >
                        Ajouter au panier
                    </button>

                    <!-- Sticky mobile add-to-cart bar -->
                    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-paper p-4 lg:hidden">
                        <button
                            type="submit"
                            class="w-full bg-ink py-4 text-xs uppercase tracking-widest2 text-paper disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="!inStock"
                        >
                            Ajouter au panier &middot; {{ number_format($product->price, 2) }} MAD
                        </button>
                    </div>
                </form>

                @if ($product->composition)
                    <details class="mt-8 border-t border-line py-4 text-sm">
                        <summary class="cursor-pointer uppercase tracking-widest2 text-xs text-stone">Composition &amp; entretien</summary>
                        <p class="mt-3 text-stone">{{ $product->composition }}</p>
                        <p class="mt-2 text-stone">{{ $product->care_instructions }}</p>
                    </details>
                @endif
            </div>
        </div>

        <dialog id="size-guide" class="w-full max-w-md rounded-sm p-8 backdrop:bg-ink/50">
            <h2 class="text-sm uppercase tracking-widest2">Guide des tailles</h2>
            <table class="mt-4 w-full text-left text-sm">
                <thead><tr class="text-stone"><th>Taille</th><th>Poitrine (cm)</th><th>Taille (cm)</th></tr></thead>
                <tbody>
                    <tr><td>XS</td><td>82</td><td>62</td></tr>
                    <tr><td>S</td><td>86</td><td>66</td></tr>
                    <tr><td>M</td><td>90</td><td>70</td></tr>
                    <tr><td>L</td><td>96</td><td>76</td></tr>
                    <tr><td>XL</td><td>102</td><td>82</td></tr>
                </tbody>
            </table>
            <button onclick="document.getElementById('size-guide').close()" class="mt-6 w-full border border-ink py-2 text-xs uppercase tracking-widest2">Fermer</button>
        </dialog>

        @if ($related->isNotEmpty())
            <section class="mt-20">
                <h2 class="text-sm uppercase tracking-widest2">Vous aimerez aussi</h2>
                <div class="mt-8 grid grid-cols-2 gap-x-2 gap-y-8 sm:grid-cols-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-shop-layout>
