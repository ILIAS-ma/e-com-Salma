<x-shop-layout>
    @php
        $campaign = fn ($category, $size) => "https://picsum.photos/seed/salma-campaign-{$category->id}/{$size}";
        $hero = $categories->first();
        $pair = $categories->slice(1, 2);
        $closing = $categories->slice(3, 2);
    @endphp

    <!-- Full-screen campaign -->
    @if ($hero)
        <a href="{{ route('shop.category', $hero) }}" class="group relative block h-[calc(100svh-4rem)] overflow-hidden bg-sand lg:h-[calc(100svh-5rem)]">
            <img src="{{ $campaign($hero, '1920/1200') }}" alt="{{ $hero->name }}" class="h-full w-full object-cover">
            <div class="absolute inset-x-0 bottom-10 text-center text-paper [text-shadow:0_0_18px_rgba(0,0,0,0.25)]">
                <p class="text-[13px] uppercase tracking-widest3">{{ $hero->name }}</p>
                <p class="label mt-3 inline-block underline underline-offset-4">Nouvelle collection</p>
            </div>
        </a>
    @endif

    <!-- Split campaign -->
    @if ($pair->isNotEmpty())
        <div class="grid grid-cols-1 gap-px md:grid-cols-2">
            @foreach ($pair as $category)
                <a href="{{ route('shop.category', $category) }}" class="relative block h-[80svh] overflow-hidden bg-sand md:h-[90svh]">
                    <img src="{{ $campaign($category, '1000/1400') }}" alt="{{ $category->name }}" loading="lazy" class="h-full w-full object-cover">
                    <p class="absolute inset-x-0 bottom-8 text-center text-[13px] uppercase tracking-widest3 text-paper [text-shadow:0_0_18px_rgba(0,0,0,0.25)]">{{ $category->name }}</p>
                </a>
            @endforeach
        </div>
    @endif

    <!-- New arrivals -->
    @if ($newArrivals->isNotEmpty())
        <section class="px-4 pt-20 sm:px-6 lg:px-10">
            <div class="flex items-baseline justify-between">
                <h2 class="section-title">Nouveautés</h2>
                <a href="{{ route('shop.index') }}" class="label link-underline">Voir tout</a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-x-2 gap-y-10 lg:grid-cols-4">
                @foreach ($newArrivals as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Closing campaign -->
    @if ($closing->isNotEmpty())
        <div class="mt-20 grid grid-cols-1 gap-px md:grid-cols-2">
            @foreach ($closing as $category)
                <a href="{{ route('shop.category', $category) }}" class="relative block h-[80svh] overflow-hidden bg-sand md:h-[90svh]">
                    <img src="{{ $campaign($category, '1000/1400') }}" alt="{{ $category->name }}" loading="lazy" class="h-full w-full object-cover">
                    <p class="absolute inset-x-0 bottom-8 text-center text-[13px] uppercase tracking-widest3 text-paper [text-shadow:0_0_18px_rgba(0,0,0,0.25)]">{{ $category->name }}</p>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Featured: horizontal strip -->
    @if ($featured->isNotEmpty())
        <section class="pt-20">
            <div class="flex items-baseline justify-between px-4 sm:px-6 lg:px-10">
                <h2 class="section-title">Sélection</h2>
                <a href="{{ route('shop.index') }}" class="label link-underline">Voir tout</a>
            </div>
            <div class="no-scrollbar mt-6 flex snap-x snap-mandatory gap-2 overflow-x-auto scroll-px-4 px-4 sm:scroll-px-6 sm:px-6 lg:scroll-px-10 lg:px-10">
                @foreach ($featured as $product)
                    <div class="w-[44vw] shrink-0 snap-start sm:w-[30vw] lg:w-[18vw]">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-shop-layout>
