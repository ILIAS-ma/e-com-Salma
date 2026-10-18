<div
    id="quick-view"
    x-data
    x-show="$store.quickView.open"
    x-cloak
    class="fixed inset-0 z-[100]"
    role="dialog"
    aria-modal="true"
    :aria-label="$store.quickView.product?.name ?? $store.quickView.previewName"
>
    <!-- Animated by resources/js/quickview.js (GSAP) -->
    <div data-qv-backdrop class="absolute inset-0 bg-black/20"></div>

    <aside
        data-qv-panel
        class="absolute inset-x-0 bottom-0 flex max-h-[88dvh] flex-col bg-paper md:inset-y-0 md:left-auto md:right-0 md:max-h-none md:w-[460px]"
        x-data="{
            get product() { return $store.quickView.product },
            selectedColor: null,
            selectedSize: null,
            adding: false,
            added: false,
            get images() {
                return this.product?.images?.length ? this.product.images : [$store.quickView.previewImage].filter(Boolean);
            },
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
            formatPrice(value) {
                return Number(value).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MAD';
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
                        setTimeout(() => { this.added = false; }, 2200);
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
    >
        <div class="flex h-14 shrink-0 items-center justify-between px-5">
            <p class="label">Ajouter</p>
            <button type="button" data-qv-close class="-mr-1 p-1" aria-label="Fermer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="square" d="M5 5l14 14M19 5L5 19" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <!-- Gallery: horizontal strip -->
            <div class="no-scrollbar flex snap-x snap-mandatory gap-px overflow-x-auto">
                <template x-for="image in images" :key="image">
                    <div class="aspect-[2/3] w-[62%] shrink-0 snap-start bg-sand md:w-[58%]">
                        <img :src="image" :alt="product?.name ?? $store.quickView.previewName" class="h-full w-full object-cover">
                    </div>
                </template>
            </div>

            <div class="px-5 pb-6 pt-6">
                <p class="text-[12px] uppercase tracking-wide" x-text="product?.name ?? $store.quickView.previewName"></p>

                <template x-if="! product">
                    <div class="mt-4 space-y-2">
                        <div class="h-2.5 w-20 animate-pulse bg-sand"></div>
                        <div class="h-2.5 w-full animate-pulse bg-sand"></div>
                        <div class="h-2.5 w-2/3 animate-pulse bg-sand"></div>
                    </div>
                </template>

                <template x-if="product">
                    <div class="menu-panel">
                        <p class="mt-1.5 flex gap-2 text-[12px]">
                            <span x-show="product.compare_at_price" class="text-stone line-through" x-text="formatPrice(product.compare_at_price ?? 0)"></span>
                            <span :class="product.compare_at_price ? 'text-brand' : ''" x-text="formatPrice(product.price)"></span>
                        </p>

                        <div class="mt-6 flex items-center gap-3">
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
                    </div>
                </template>
            </div>
        </div>

        <div class="shrink-0 border-t border-line bg-paper p-5">
            <button type="button" @click="addToCart()" :disabled="!inStock || adding" class="btn-primary w-full">
                <span x-show="!adding && !added" x-text="selectedSize ? 'Ajouter au panier' : 'Sélectionnez une taille'"></span>
                <span x-show="adding" x-cloak>Ajout…</span>
                <span x-show="added" x-cloak>Ajouté au panier</span>
            </button>
            <a :href="product?.url" x-show="product" class="label link-underline mt-4 block w-fit mx-auto">Voir le produit</a>
        </div>
    </aside>
</div>
