<div
    id="quick-view"
    x-data
    x-show="$store.quickView.open"
    x-cloak
    class="fixed inset-0 z-[100]"
    role="dialog"
    aria-modal="true"
>
    <div
        data-qv-backdrop
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-paper/40 backdrop-blur-md"
    ></div>

    <div class="relative flex h-full items-start justify-center overflow-y-auto px-4 py-8 sm:items-center sm:p-10">
        <div
            data-qv-panel
            x-transition:enter="transition ease-out duration-[250ms]"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="relative grid w-full max-w-5xl grid-cols-1 bg-paper shadow-[0_20px_60px_rgba(0,0,0,0.25)] lg:grid-cols-[3fr_2fr]"
        >
            <button type="button" data-qv-close class="absolute right-3 top-3 z-10 bg-paper/90 p-2 text-ink/70 hover:text-ink sm:right-5 sm:top-5" aria-label="Fermer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div
                x-data="{
                    get product() { return $store.quickView.product },
                    get landed() { return $store.quickView.imageLanded },
                    selectedColor: null,
                    selectedSize: null,
                    adding: false,
                    added: false,
                    get colors() {
                        if (! this.product) return [];
                        const seen = new Set();
                        return this.product.variants.filter(v => {
                            if (seen.has(v.color_name)) return false;
                            seen.add(v.color_name);
                            return true;
                        }).map(v => ({ name: v.color_name, code: v.color_code }));
                    },
                    get sizes() {
                        return this.product ? [...new Set(this.product.variants.map(v => v.size))] : [];
                    },
                    get selectedVariant() {
                        return this.product?.variants.find(v => v.color_name === this.selectedColor && v.size === this.selectedSize) ?? null;
                    },
                    get inStock() {
                        return this.selectedVariant && this.selectedVariant.stock > 0;
                    },
                    sizeAvailable(size) {
                        return this.product?.variants.some(v => v.size === size && v.color_name === this.selectedColor && v.stock > 0);
                    },
                    async addToCart() {
                        if (! this.inStock || this.adding) return;
                        this.adding = true;
                        try {
                            const res = await fetch('{{ route('cart.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                },
                                body: JSON.stringify({ variant_id: this.selectedVariant.id, quantity: 1 }),
                            });
                            const data = await res.json();
                            if (res.ok) {
                                $store.cart.count = data.cart_count;
                                this.added = true;
                                setTimeout(() => { this.added = false; }, 2000);
                            }
                        } finally {
                            this.adding = false;
                        }
                    },
                }"
                x-init="$watch('product', (p) => {
                    selectedColor = p?.variants?.[0]?.color_name ?? null;
                    selectedSize = null;
                    added = false;
                })"
                class="contents"
            >
                <!-- Image column: the main photo only appears once the flying
                     clone has landed here, so there is never a double image.
                     The rest of the gallery appears alongside it, just like
                     the full product page would show. -->
                <div class="p-2 sm:p-3">
                    <div data-qv-image-slot class="relative aspect-[3/4] overflow-hidden bg-sand">
                        <img
                            x-show="landed"
                            x-cloak
                            :src="product?.images?.[0]"
                            class="h-full w-full object-cover"
                            alt=""
                        >
                    </div>

                    <div x-show="landed && product?.images?.length > 1" x-cloak class="no-scrollbar mt-2 flex snap-x snap-mandatory gap-2 overflow-x-auto">
                        <template x-for="image in (product?.images ?? []).slice(1)" :key="image">
                            <div class="aspect-[3/4] w-24 shrink-0 snap-start overflow-hidden bg-sand sm:w-28">
                                <img :src="image" class="h-full w-full object-cover" alt="">
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex max-h-[85vh] flex-col overflow-y-auto p-6 sm:p-10">
                    <template x-if="! product">
                        <div class="animate-pulse space-y-3">
                            <div class="h-3 w-1/3 bg-sand"></div>
                            <div class="h-5 w-2/3 bg-sand"></div>
                            <div class="mt-4 h-4 w-1/4 bg-sand"></div>
                        </div>
                    </template>

                    <div
                        x-show="product"
                        x-cloak
                        x-transition:enter="transition ease-out duration-[400ms]"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                    >
                        <template x-if="product">
                            <div>
                                <p class="text-[11px] uppercase tracking-widest2 text-stone" x-text="product.collection"></p>
                                <h2 class="mt-2 text-lg" x-text="product.name"></h2>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-sm text-ink" x-text="product.price.toFixed(2) + ' MAD'"></span>
                                    <span x-show="product.compare_at_price" class="text-sm text-stone/60 line-through" x-text="(product.compare_at_price ?? 0).toFixed(2) + ' MAD'"></span>
                                </div>

                                <p class="mt-4 whitespace-pre-line text-xs leading-relaxed text-stone" x-text="product.description"></p>

                                <div class="mt-6">
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

                                <div class="mt-6">
                                    <p class="mb-3 text-[11px] uppercase tracking-widest2 text-stone">Taille</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="size in sizes" :key="size">
                                            <button
                                                type="button"
                                                class="h-10 w-10 border text-sm transition"
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

                                <button
                                    type="button"
                                    @click="addToCart()"
                                    :disabled="!inStock || adding"
                                    class="mt-8 w-full bg-ink py-3.5 text-xs uppercase tracking-widest2 text-paper transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    <span x-show="!adding && !added">Ajouter au panier</span>
                                    <span x-show="adding" x-cloak>Ajout…</span>
                                    <span x-show="added" x-cloak>Ajouté &#10003;</span>
                                </button>

                                <div x-show="product.composition" class="mt-8 border-t border-line pt-4">
                                    <details>
                                        <summary class="cursor-pointer text-[11px] uppercase tracking-widest2 text-stone">Composition &amp; entretien</summary>
                                        <p class="mt-3 text-xs leading-relaxed text-stone" x-text="product.composition"></p>
                                        <p class="mt-2 text-xs leading-relaxed text-stone" x-text="product.care_instructions"></p>
                                    </details>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<img id="quick-view-flyer" src="" alt="" style="display: none;">
