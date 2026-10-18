@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">
    <title>{{ $title ? $title.' | '.config('app.name') : config('app.name').' | Silk & More' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="flex min-h-screen flex-col"
    :class="{ 'overflow-hidden': menuOpen }"
    x-data="{ menuOpen: false, menuTab: {{ $navCategories->first()?->id ?? 'null' }} }"
    @keydown.escape.window="menuOpen = false"
    data-cart-count="{{ $cartCount ?? 0 }}"
>
    <header class="sticky top-0 z-40 bg-paper">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:h-20 lg:px-10">
            <div class="flex items-center gap-4 lg:gap-8">
                <button class="-ml-1 p-1" @click="menuOpen = true" aria-label="Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="square" d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                </button>
                <a href="{{ route('home') }}" aria-label="{{ config('app.name') }} — accueil">
                    <img src="{{ asset('images/salma-logo.png') }}" alt="{{ config('app.name') }} — Silk & More" class="h-11 w-auto lg:h-14" width="131" height="84">
                </a>
            </div>

            <nav class="flex items-center gap-5 text-[11px] uppercase tracking-widest2 lg:gap-8">
                @auth
                    <a href="{{ route('orders.index') }}" class="link-underline hidden md:inline">Mon compte</a>
                @else
                    <a href="{{ route('login') }}" class="link-underline hidden md:inline">Se connecter</a>
                @endauth
                <a href="{{ route('pages.faq') }}" class="link-underline hidden md:inline">Aide</a>
                <a href="{{ route('cart.index') }}" class="link-underline">
                    Panier (<span x-data x-text="$store.cart.count">{{ $cartCount ?? 0 }}</span>)
                </a>
            </nav>
        </div>
    </header>

    <!-- Menu drawer -->
    <div
        class="fixed inset-0 z-50 bg-black/20"
        x-show="menuOpen"
        x-cloak
        x-transition:enter="transition-opacity duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="menuOpen = false"
    ></div>

    <aside
        x-show="menuOpen"
        x-cloak
        class="fixed left-0 top-0 z-50 flex h-dvh w-full flex-col overflow-y-auto bg-paper sm:w-[440px]"
        x-transition:enter="drawer-enter"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="drawer-leave"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        aria-label="Menu principal"
    >
        <div class="flex h-16 items-center px-4 sm:px-6 lg:h-20 lg:px-10">
            <button class="-ml-1 p-1" @click="menuOpen = false" aria-label="Fermer le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="square" d="M5 5l14 14M19 5L5 19" />
                </svg>
            </button>
        </div>

        <div class="px-4 pb-10 pt-4 sm:px-6 lg:px-10">
            <!-- Root categories as tabs -->
            <div class="no-scrollbar flex gap-6 overflow-x-auto text-[13px] uppercase tracking-widest2">
                @foreach ($navCategories as $navCategory)
                    <button
                        type="button"
                        @click="menuTab = {{ $navCategory->id }}"
                        class="link-underline shrink-0 uppercase"
                        :class="{ 'is-active': menuTab === {{ $navCategory->id }}, 'text-stone': menuTab !== {{ $navCategory->id }} }"
                    >{{ $navCategory->name }}</button>
                @endforeach
            </div>

            @foreach ($navCategories as $navCategory)
                <ul x-show="menuTab === {{ $navCategory->id }}" class="menu-panel mt-10 space-y-3.5 text-[12px] uppercase tracking-wide">
                    <li>
                        <a href="{{ route('shop.category', $navCategory) }}" class="link-underline" @click="menuOpen = false">Voir tout</a>
                    </li>
                    @foreach ($navCategory->children as $child)
                        <li>
                            <a href="{{ route('shop.category', $child) }}" class="link-underline" @click="menuOpen = false">{{ $child->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endforeach

            <ul class="mt-12 space-y-3.5 border-t border-line pt-8 text-[12px] uppercase tracking-wide">
                <li><a href="{{ route('shop.index', ['sale' => 1]) }}" class="link-underline text-brand">Promotions</a></li>
                <li><a href="{{ route('shop.index') }}" class="link-underline">Toute la boutique</a></li>
                <li><a href="{{ route('orders.track') }}" class="link-underline">Suivi de commande</a></li>
                @auth
                    <li><a href="{{ route('orders.index') }}" class="link-underline">Mon compte</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="link-underline">Se connecter</a></li>
                @endauth
                <li><a href="{{ route('pages.faq') }}" class="link-underline">Aide</a></li>
            </ul>
        </div>
    </aside>

    <!-- Flash messages -->
    @if (session('success') || session('error'))
        <div
            x-data="{ show: false }"
            x-init="$nextTick(() => show = true); setTimeout(() => show = false, 4500)"
            x-show="show"
            x-cloak
            x-transition:enter="transition duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-x-0 bottom-0 z-[90] flex items-center justify-between gap-4 bg-ink px-4 py-4 text-[11px] uppercase tracking-widest2 text-paper sm:inset-x-auto sm:bottom-6 sm:right-6 sm:min-w-[360px]"
            role="status"
        >
            <p>{{ session('success') ?? session('error') }}</p>
            <button @click="show = false" class="text-base leading-none" aria-label="Fermer">&times;</button>
        </div>
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-32 px-4 pb-10 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-xl text-center" x-data="{ sent: false }">
            <p class="label">Inscrivez-vous à notre newsletter</p>
            <form class="mt-6 flex items-end gap-4" @submit.prevent="sent = true" x-show="!sent">
                <input type="email" required placeholder="E-mail" class="field">
                <button type="submit" class="label link-underline shrink-0 pb-2.5">S'inscrire</button>
            </form>
            <p x-show="sent" x-cloak class="label mt-6">Merci, votre inscription est confirmée.</p>
        </div>

        <x-social-links class="mt-16 flex flex-wrap justify-center gap-x-8 gap-y-3" />

        <div class="mt-16 flex flex-wrap justify-center gap-x-8 gap-y-3 text-[11px] uppercase tracking-widest2 text-stone">
            <a href="{{ route('orders.track') }}" class="link-underline hover:text-ink">Suivi de commande</a>
            <a href="{{ route('pages.faq') }}" class="link-underline hover:text-ink">FAQ</a>
            <a href="{{ route('pages.about') }}" class="link-underline hover:text-ink">Qui sommes-nous</a>
            <span>Paiement à la livraison</span>
            <span>Livraison partout au Maroc</span>
        </div>

        <p class="mt-10 text-center text-[10px] uppercase tracking-widest2 text-stone">&copy; {{ date('Y') }} {{ config('app.name') }} — Silk &amp; More</p>
    </footer>

    <x-quick-view-modal />
</body>
</html>
