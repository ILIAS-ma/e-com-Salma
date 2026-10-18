<x-shop-layout :title="$category?->name">
    @php
        // Sub-navigation: a category's children, or its siblings, or the root categories.
        $subnav = $category
            ? ($category->children->isNotEmpty() ? $category->children : ($category->parent?->children ?? collect()))
            : $categories;
        $subnavParent = $category?->children->isNotEmpty() ? $category : $category?->parent;
        $activeFilters = request()->only(['gender', 'size', 'color', 'min_price', 'max_price', 'sort']);
    @endphp

    <div
        x-data="{
            filtersOpen: false,
            columns: 4,
            transitioning: false,
            setColumns(n) {
                if (this.columns === n) return;
                this.transitioning = true;
                setTimeout(() => {
                    this.columns = n;
                    requestAnimationFrame(() => { this.transitioning = false; });
                }, 200);
            },
        }"
        @keydown.escape.window="filtersOpen = false"
        class="px-4 pt-6 sm:px-6 lg:px-10"
    >
        <!-- Sub-navigation -->
        @if ($subnav->isNotEmpty())
            <nav class="no-scrollbar -mx-4 flex gap-6 overflow-x-auto px-4 text-[11px] uppercase tracking-widest2 sm:mx-0 sm:px-0">
                @if ($subnavParent)
                    <a href="{{ route('shop.category', $subnavParent) }}" @class(['link-underline shrink-0', 'is-active' => $category?->is($subnavParent), 'text-stone' => ! $category?->is($subnavParent)])>Voir tout</a>
                @endif
                @foreach ($subnav as $item)
                    <a href="{{ route('shop.category', $item) }}" @class(['link-underline shrink-0', 'is-active' => $category?->is($item), 'text-stone' => ! $category?->is($item)])>{{ $item->name }}</a>
                @endforeach
            </nav>
        @endif

        <!-- Toolbar -->
        <div class="mt-8 flex items-center justify-between">
            <h1 class="section-title">
                {{ $category?->name ?? (request()->boolean('sale') ? 'Promotions' : 'Toute la boutique') }}
                <span class="ml-2 text-stone">{{ $products->total() }}</span>
            </h1>

            <div class="flex items-center gap-6">
                <!-- Grid density (desktop) -->
                <div class="hidden items-center gap-3 lg:flex">
                    @foreach ([2 => 'M3 3h8v18H3zM13 3h8v18h-8z', 4 => 'M2 3h4v18H2zM7.33 3h4v18h-4zM12.67 3h4v18h-4zM18 3h4v18h-4z', 6 => 'M1 3h2.5v18H1zM4.7 3h2.5v18H4.7zM8.4 3h2.5v18H8.4zM12.1 3h2.5v18h-2.5zM15.8 3h2.5v18h-2.5zM19.5 3H22v18h-2.5z'] as $count => $icon)
                        <button
                            type="button"
                            @click="setColumns({{ $count }})"
                            :class="columns === {{ $count }} ? 'opacity-100' : 'opacity-30 hover:opacity-60'"
                            class="transition-opacity duration-200"
                            aria-label="{{ $count }} par ligne"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $icon }}" /></svg>
                        </button>
                    @endforeach
                </div>

                <button type="button" @click="filtersOpen = true" class="label link-underline">
                    Filtres @if (count(array_filter($activeFilters)))({{ count(array_filter($activeFilters)) }})@endif
                </button>
            </div>
        </div>

        <!-- Grid -->
        @if ($products->isEmpty())
            <div class="py-32 text-center">
                <p class="label">Aucun article ne correspond à ces critères</p>
                <a href="{{ route('shop.index') }}" class="btn-outline mt-8">Voir toute la boutique</a>
            </div>
        @else
            <div
                class="mt-6 grid grid-cols-2 gap-x-2 gap-y-10 transition-opacity duration-200"
                :class="{
                    'lg:grid-cols-2': columns === 2,
                    'lg:grid-cols-4': columns === 4,
                    'lg:grid-cols-6': columns === 6,
                    'opacity-0': transitioning,
                }"
            >
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-16">{{ $products->links() }}</div>
        @endif

        <!-- Filters drawer -->
        <div
            class="fixed inset-0 z-50 bg-black/20"
            x-show="filtersOpen"
            x-cloak
            x-transition:enter="transition-opacity duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="filtersOpen = false"
        ></div>

        <aside
            x-show="filtersOpen"
            x-cloak
            class="fixed right-0 top-0 z-50 flex h-dvh w-full flex-col bg-paper sm:w-[420px]"
            x-transition:enter="drawer-enter"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="drawer-leave"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            aria-label="Filtres"
        >
            <div class="flex h-16 shrink-0 items-center justify-between px-6 lg:h-20">
                <p class="label">Filtres</p>
                <button type="button" @click="filtersOpen = false" class="-mr-1 p-1" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="square" d="M5 5l14 14M19 5L5 19" />
                    </svg>
                </button>
            </div>

            <form
                method="GET"
                class="flex flex-1 flex-col overflow-hidden"
                @change="$el.querySelectorAll('.chip').forEach(chip => chip.classList.toggle('is-active', chip.querySelector('input').checked))"
            >
                @if (request()->boolean('sale'))
                    <input type="hidden" name="sale" value="1">
                @endif

                <div class="flex-1 space-y-10 overflow-y-auto px-6 pb-8">
                    <div>
                        <p class="label text-stone">Trier par</p>
                        <div class="mt-4 space-y-3 text-[12px] uppercase tracking-wide">
                            @foreach (['newest' => 'Nouveautés', 'popularity' => 'Popularité', 'price_asc' => 'Prix croissant', 'price_desc' => 'Prix décroissant'] as $value => $label)
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="radio" name="sort" value="{{ $value }}" class="h-3 w-3 rounded-none border-ink text-ink focus:ring-0" @checked(request('sort', 'newest') === $value)>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="label text-stone">Genre</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (['femme' => 'Femme', 'homme' => 'Homme', 'enfant' => 'Enfant'] as $value => $label)
                                <label @class(['chip', 'is-active' => request('gender') === $value])>
                                    <input type="radio" name="gender" value="{{ $value }}" class="hidden" @checked(request('gender') === $value)>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="label text-stone">Taille</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($sizes as $size)
                                <label @class(['chip', 'is-active' => request('size') === $size])>
                                    <input type="radio" name="size" value="{{ $size }}" class="hidden" @checked(request('size') === $size)>
                                    {{ $size }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="label text-stone">Couleur</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($colors as $color)
                                <label @class(['chip', 'is-active' => request('color') === $color])>
                                    <input type="radio" name="color" value="{{ $color }}" class="hidden" @checked(request('color') === $color)>
                                    {{ $color }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="label text-stone">Prix (MAD)</p>
                        <div class="mt-2 flex gap-6">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="field">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="field">
                        </div>
                    </div>
                </div>

                <div class="grid shrink-0 grid-cols-2 gap-2 border-t border-line p-6">
                    <a href="{{ $category ? route('shop.category', $category) : route('shop.index', request()->boolean('sale') ? ['sale' => 1] : []) }}" class="btn-outline">Effacer</a>
                    <button type="submit" class="btn-primary">Voir les résultats</button>
                </div>
            </form>
        </aside>
    </div>
</x-shop-layout>
