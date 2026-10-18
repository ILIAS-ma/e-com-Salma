<x-shop-layout :title="$category?->name">
    <div
        class="mx-auto max-w-[1600px] px-4 py-8 sm:px-6"
        x-data="{
            filtersOpen: true,
            columns: 3,
            transitioning: false,
            setColumns(n) {
                if (this.columns === n) return;
                this.transitioning = true;
                setTimeout(() => {
                    this.columns = n;
                    requestAnimationFrame(() => { this.transitioning = false; });
                }, 180);
            },
        }"
    >
        <h1 class="text-sm uppercase tracking-widest2">
            {{ $category?->name ?? (request()->boolean('sale') ? 'Promotions' : 'Toute la boutique') }}
        </h1>

        <!-- Toolbar: filter toggle, count, sort, column density -->
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-b border-line pb-4">
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    @click="filtersOpen = !filtersOpen"
                    class="flex items-center gap-2 text-[11px] uppercase tracking-widest2"
                >
                    <span x-text="filtersOpen ? 'Masquer les filtres' : 'Afficher les filtres'"></span>
                    <span x-text="filtersOpen ? '−' : '+'"></span>
                </button>
                <p class="text-xs text-stone">{{ $products->total() }} article(s)</p>
            </div>

            <div class="flex items-center gap-5">
                <form method="GET">
                    @foreach (request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="rounded-none border-line text-xs">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Nouveautés</option>
                        <option value="popularity" @selected(request('sort') === 'popularity')>Popularité</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Prix croissant</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Prix décroissant</option>
                    </select>
                </form>

                <!-- Column density, like Zara's grid switcher (desktop only) -->
                <div class="hidden items-center gap-2 lg:flex">
                    <button type="button" @click="setColumns(2)" :class="columns === 2 ? 'text-ink' : 'text-stone'" class="text-[11px] uppercase tracking-widest2 transition-colors duration-200" title="2 par ligne">2</button>
                    <span class="text-line">/</span>
                    <button type="button" @click="setColumns(3)" :class="columns === 3 ? 'text-ink' : 'text-stone'" class="text-[11px] uppercase tracking-widest2 transition-colors duration-200" title="3 par ligne">3</button>
                    <span class="text-line">/</span>
                    <button type="button" @click="setColumns(4)" :class="columns === 4 ? 'text-ink' : 'text-stone'" class="text-[11px] uppercase tracking-widest2 transition-colors duration-200" title="4 par ligne">4</button>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8" :class="filtersOpen ? 'lg:grid-cols-[200px_1fr]' : 'lg:grid-cols-1'">
            <!-- Filters -->
            <aside x-show="filtersOpen" x-transition.opacity>
                <form method="GET" class="space-y-7 text-xs">
                    <div>
                        <p class="mb-3 text-[10px] uppercase tracking-widest2 text-stone">Genre</p>
                        <div class="space-y-2 text-ink">
                            @foreach (['femme' => 'Femme', 'homme' => 'Homme', 'enfant' => 'Enfant'] as $value => $label)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="gender" value="{{ $value }}" @checked(request('gender') === $value) onchange="this.form.submit()">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="mb-3 text-[10px] uppercase tracking-widest2 text-stone">Taille</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($sizes as $size)
                                <label class="cursor-pointer border px-2.5 py-1 text-[11px] {{ request('size') === $size ? 'border-ink bg-ink text-paper' : 'border-line text-ink' }}">
                                    <input type="radio" name="size" value="{{ $size }}" class="hidden" @checked(request('size') === $size) onchange="this.form.submit()">
                                    {{ $size }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="mb-3 text-[10px] uppercase tracking-widest2 text-stone">Couleur</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($colors as $color)
                                <label class="cursor-pointer border px-2.5 py-1 text-[11px] {{ request('color') === $color ? 'border-ink bg-ink text-paper' : 'border-line text-ink' }}">
                                    <input type="radio" name="color" value="{{ $color }}" class="hidden" @checked(request('color') === $color) onchange="this.form.submit()">
                                    {{ $color }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="mb-3 text-[10px] uppercase tracking-widest2 text-stone">Prix (MAD)</p>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full rounded-none border-line text-xs">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full rounded-none border-line text-xs">
                        </div>
                        <button class="mt-3 w-full border border-ink py-1.5 text-[11px] uppercase tracking-widest2">Appliquer</button>
                    </div>

                    @if (request()->anyFilled(['gender', 'size', 'color', 'min_price', 'max_price', 'collection']))
                        <a href="{{ route('shop.index') }}" class="block text-[11px] uppercase tracking-widest2 text-stone underline">Réinitialiser</a>
                    @endif
                </form>
            </aside>

            <!-- Grid -->
            <div>
                @if ($products->isEmpty())
                    <p class="py-20 text-center text-stone">Aucun article ne correspond à ces critères.</p>
                @else
                    <div
                        class="grid grid-cols-2 gap-x-2 gap-y-8 transition-all duration-200 ease-out"
                        :class="{
                            'lg:grid-cols-2': columns === 2,
                            'lg:grid-cols-3': columns === 3,
                            'lg:grid-cols-4': columns === 4,
                            'opacity-0 scale-[0.98]': transitioning,
                            'opacity-100 scale-100': !transitioning,
                        }"
                    >
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-12">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-shop-layout>
