<x-shop-layout>
    <!-- Hero: full-bleed, no color block, minimal caption -->
    <section class="relative">
        <div class="relative aspect-[4/5] w-full overflow-hidden bg-sand sm:aspect-[16/9] lg:aspect-[21/9]">
            <img src="https://picsum.photos/seed/atlas-hero/1800/900" alt="Collection" class="h-full w-full object-cover">
        </div>
        <div class="mx-auto max-w-[1600px] px-4 py-10 text-center sm:px-6">
            <p class="text-xs uppercase tracking-widest2 text-stone">Nouvelle collection</p>
            <h1 class="mt-3 text-3xl font-light sm:text-4xl">La femme, dans une matière juste</h1>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block border-b border-ink pb-0.5 text-xs uppercase tracking-widest2 hover:opacity-60">
                Découvrir
            </a>
        </div>
    </section>

    <!-- Categories: flat tiles, caption below the image (no overlay) -->
    <section class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6">
        <div class="flex snap-x snap-mandatory gap-2 overflow-x-auto pb-2 no-scrollbar sm:gap-3">
            @foreach ($categories as $category)
                <a
                    href="{{ route('shop.category', $category) }}"
                    class="block w-[48vw] shrink-0 snap-start sm:w-[24vw] lg:w-[16vw]"
                    x-data="{ shown: false }" x-intersect.once="shown = true" data-reveal :class="{ 'is-visible': shown }"
                >
                    <div class="aspect-[3/4] overflow-hidden bg-sand">
                        <img src="https://picsum.photos/seed/cat-{{ $category->id }}/500/650" alt="{{ $category->name }}" class="h-full w-full object-cover">
                    </div>
                    <p class="mt-2 text-center text-xs uppercase tracking-widest2 text-ink">{{ $category->name }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <x-editorial-statement />

    <!-- Featured -->
    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-[1600px] px-4 py-14 sm:px-6">
            <div class="flex items-baseline justify-between">
                <h2 class="text-base uppercase tracking-widest2">Sélection</h2>
                <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-widest2 text-stone hover:text-ink">Tout voir</a>
            </div>
            <div class="mt-6 flex snap-x snap-mandatory gap-2 overflow-x-auto pb-2 no-scrollbar sm:gap-3">
                @foreach ($featured as $product)
                    <div class="w-[48vw] shrink-0 snap-start sm:w-[32vw] lg:w-[19vw]">
                        <x-product-card :product="$product" :large-text="true" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- New arrivals -->
    <section class="mx-auto max-w-[1600px] px-4 py-14 sm:px-6">
        <div class="flex items-baseline justify-between">
            <h2 class="text-base uppercase tracking-widest2">Nouveautés</h2>
            <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-widest2 text-stone hover:text-ink">Tout voir</a>
        </div>
        <div class="mt-6 flex snap-x snap-mandatory gap-2 overflow-x-auto pb-2 no-scrollbar sm:gap-3">
            @foreach ($newArrivals as $product)
                <div class="w-[48vw] shrink-0 snap-start sm:w-[32vw] lg:w-[19vw]">
                    <x-product-card :product="$product" :large-text="true" />
                </div>
            @endforeach
        </div>
    </section>

    <!-- Info strip -->
    <section class="border-t border-line py-10">
        <div class="mx-auto flex max-w-[1600px] snap-x snap-mandatory gap-4 overflow-x-auto px-4 text-center no-scrollbar sm:px-6">
            <p class="w-[80vw] shrink-0 snap-start text-sm text-stone sm:w-[33%]">Livraison partout au Maroc</p>
            <p class="w-[80vw] shrink-0 snap-start text-sm text-stone sm:w-[33%]">Paiement à la livraison disponible</p>
            <p class="w-[80vw] shrink-0 snap-start text-sm text-stone sm:w-[33%]">Retours sous 14 jours</p>
        </div>
    </section>

    <!-- FAQ preview -->
    <section class="border-t border-line py-16">
        <div class="mx-auto max-w-2xl px-4 sm:px-6">
            <p class="text-center text-xs uppercase tracking-widest2 text-stone">Questions fréquentes</p>
            <div class="mt-8 divide-y divide-line border-y border-line">
                @foreach ($topFaqs as $question => $answer)
                    <details class="group faq-item scroll-mt-28 py-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between text-base">
                            {{ $question }}
                            <span class="text-stone group-open:hidden">+</span>
                            <span class="hidden text-stone group-open:inline">−</span>
                        </summary>
                        <p class="faq-answer mt-3 text-sm leading-relaxed text-stone">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('pages.faq') }}" class="border-b border-ink pb-0.5 text-xs uppercase tracking-widest2 hover:opacity-60">
                    Voir toutes les questions
                </a>
            </div>
        </div>
    </section>

    <x-newsletter-signup />

    <!-- Social -->
    <section class="border-t border-line py-10">
        <div class="mx-auto flex max-w-[1600px] flex-col items-center gap-4 px-4 text-center sm:px-6">
            <p class="text-xs uppercase tracking-widest2 text-stone">Suivez-nous</p>
            <x-social-links class="flex items-center gap-6 text-ink" />
        </div>
    </section>
</x-shop-layout>
