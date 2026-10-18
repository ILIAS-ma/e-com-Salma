@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">
    <title>{{ $title ?? config('app.name') }} — Vêtements &amp; bijoux femme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="flex min-h-screen flex-col bg-paper text-ink font-light"
    :class="{ 'overflow-hidden': mobileMenuOpen }"
    x-data="{ mobileMenuOpen: false }"
    @keydown.escape.window="mobileMenuOpen = false"
    data-cart-count="{{ $cartCount ?? 0 }}"
>

    <header class="sticky top-0 z-40 border-b border-line bg-paper/95 backdrop-blur-md transition-colors duration-200">
        <div class="relative mx-auto flex max-w-[1600px] items-center justify-between px-4 py-4 sm:px-6">
            <!-- Mobile cart icon (left) -->
            <div class="flex items-center lg:hidden">
                <a href="{{ route('cart.index') }}" class="relative p-1 transition-opacity duration-200 hover:opacity-70 active:scale-95" aria-label="Panier">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.591-7.152.075-.311-.163-.598-.482-.598H5.106M7.5 14.25L5.106 5.272M7.5 14.25L5.272 5.106M9.75 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm9 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <span
                        x-show="$store.cart.count > 0"
                        x-cloak
                        x-text="$store.cart.count"
                        class="absolute -right-1 -top-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-ink text-[9px] font-medium text-paper"
                    ></span>
                </a>
            </div>

            <!-- Desktop mega-menu -->
            <nav data-navbar-desktop class="hidden items-center gap-7 text-[11px] font-normal uppercase tracking-widest2 text-ink lg:flex">
                @foreach ($navCategories as $navCategory)
                    <div class="relative" data-nav-item>
                        <a
                            href="{{ route('shop.category', $navCategory) }}"
                            class="block py-2 opacity-70 hover:opacity-100"
                        >
                            {{ $navCategory->name }}
                        </a>

                        @if ($navCategory->children->isNotEmpty())
                            <div
                                data-nav-panel
                                class="absolute left-0 top-full w-56 border border-line bg-paper py-4 shadow-[0_8px_24px_rgba(0,0,0,0.06)]"
                            >
                                <ul class="space-y-2.5 px-5">
                                    @foreach ($navCategory->children as $child)
                                        <li>
                                            <a href="{{ route('shop.category', $child) }}" class="block text-[12px] normal-case tracking-normal text-ink opacity-80 hover:opacity-100">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-3 border-t border-line px-5 pt-3">
                                    <a href="{{ route('shop.category', $navCategory) }}" class="text-[10px] uppercase tracking-widest2 text-stone hover:text-ink">
                                        Voir tout &middot; {{ $navCategory->name }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
                <a href="{{ route('shop.index', ['sale' => 1]) }}" class="py-2 opacity-70 hover:opacity-100">Promotions</a>
            </nav>

            <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2 text-logo font-normal">
                {{ strtoupper(config('app.name')) }}
            </a>

            <div class="flex items-center gap-4 text-ink">
                <a href="{{ route('orders.track') }}" class="hidden text-[11px] uppercase tracking-widest2 opacity-70 hover:opacity-100 sm:inline">Suivi</a>
                @auth
                    <a href="{{ route('orders.index') }}" class="hidden text-[11px] uppercase tracking-widest2 opacity-70 hover:opacity-100 sm:inline">Compte</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-[11px] uppercase tracking-widest2 opacity-70 hover:opacity-100 sm:inline">Connexion</a>
                @endauth
                <!-- Desktop cart icon (right) -->
                <a href="{{ route('cart.index') }}" class="relative hidden p-1 transition-opacity duration-200 hover:opacity-70 lg:inline-flex" aria-label="Panier">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.591-7.152.075-.311-.163-.598-.482-.598H5.106M7.5 14.25L5.106 5.272M7.5 14.25L5.272 5.106M9.75 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm9 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <span
                        x-show="$store.cart.count > 0"
                        x-cloak
                        x-text="$store.cart.count"
                        class="absolute -right-1 -top-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-ink text-[9px] font-medium text-paper"
                    ></span>
                </a>
                <button class="p-1 transition-opacity duration-200 hover:opacity-70 active:scale-95 lg:hidden" @click="mobileMenuOpen = true" aria-label="Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu: right side panel -->
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden">
            <div
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
                x-transition:enter="transition-opacity duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-250 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="mobileMenuOpen = false"
            ></div>

            <aside
                class="fixed right-0 top-0 z-50 h-dvh w-[82vw] max-w-sm overflow-y-auto border-l border-line bg-paper px-4 pb-6 pt-5 shadow-2xl"
                x-transition:enter="drawer-transition-enter"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="drawer-transition-leave"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                <div class="mb-4 flex items-center justify-between border-b border-line pb-3">
                    <p class="text-[11px] uppercase tracking-widest2 text-stone">Menu</p>
                    <button class="p-1 transition-opacity duration-200 hover:opacity-60 active:scale-95" @click="mobileMenuOpen = false" aria-label="Fermer le menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @foreach ($navCategories as $navCategory)
                    @if ($navCategory->children->isNotEmpty())
                        <details class="mobile-nav-details group border-b border-line py-1">
                            <summary class="flex cursor-pointer list-none items-center justify-between py-2 text-xs uppercase tracking-widest2 text-ink">
                                {{ $navCategory->name }}
                                <span class="text-stone transition-transform duration-200 group-open:hidden">+</span>
                                <span class="hidden text-stone transition-transform duration-200 group-open:inline">−</span>
                            </summary>
                            <div class="mobile-submenu space-y-2 pb-3 pl-3">
                                @foreach ($navCategory->children as $child)
                                    <a href="{{ route('shop.category', $child) }}" class="block text-xs normal-case text-stone transition-colors duration-150 hover:text-ink" @click="mobileMenuOpen = false">{{ $child->name }}</a>
                                @endforeach
                                <a href="{{ route('shop.category', $navCategory) }}" class="block text-xs uppercase tracking-widest2 text-ink opacity-80 hover:opacity-100" @click="mobileMenuOpen = false">Voir tout</a>
                            </div>
                        </details>
                    @else
                        <a href="{{ route('shop.category', $navCategory) }}" class="block border-b border-line py-3 text-xs uppercase tracking-widest2 text-ink transition-colors duration-150 hover:text-stone" @click="mobileMenuOpen = false">{{ $navCategory->name }}</a>
                    @endif
                @endforeach

                <a href="{{ route('shop.index', ['sale' => 1]) }}" class="block border-b border-line py-3 text-xs uppercase tracking-widest2 text-ink transition-colors duration-150 hover:text-stone" @click="mobileMenuOpen = false">Promotions</a>
                <a href="{{ route('orders.track') }}" class="block border-b border-line py-3 text-xs uppercase tracking-widest2 text-ink transition-colors duration-150 hover:text-stone" @click="mobileMenuOpen = false">Suivi de commande</a>
                @auth
                    <a href="{{ route('orders.index') }}" class="block py-3 text-xs uppercase tracking-widest2 text-ink transition-colors duration-150 hover:text-stone" @click="mobileMenuOpen = false">Mon Compte</a>
                @else
                    <a href="{{ route('login') }}" class="block py-3 text-xs uppercase tracking-widest2 text-ink transition-colors duration-150 hover:text-stone" @click="mobileMenuOpen = false">Connexion</a>
                @endauth
            </aside>
        </div>
    </header>

    @if (session('success'))
        <div class="border-b border-line px-4 py-2.5 text-center text-xs text-ink">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="border-b border-line px-4 py-2.5 text-center text-xs text-ink">{{ session('error') }}</div>
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-24 border-t border-line">
        <div class="mx-auto grid max-w-[1600px] gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4">
            <div>
                <p class="text-[13px] tracking-widest3">{{ strtoupper(config('app.name')) }}</p>
                <p class="mt-4 text-xs leading-relaxed text-stone">Vêtements en matières nobles et bijoux fins.</p>

                <x-social-links class="mt-5 flex items-center gap-4 text-ink" />
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest2 text-stone">Aide</p>
                <ul class="mt-4 space-y-2 text-xs text-ink/80">
                    <li><a href="{{ route('orders.track') }}" class="hover:opacity-60">Suivre ma commande</a></li>
                    <li><a href="{{ route('pages.faq') }}" class="hover:opacity-60">FAQ</a></li>
                    <li><a href="{{ route('pages.about') }}" class="hover:opacity-60">Qui sommes-nous</a></li>
                    <li>Guide des tailles</li>
                </ul>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest2 text-stone">Paiement</p>
                <ul class="mt-4 space-y-2 text-xs text-ink/80">
                    <li>Carte bancaire (CMI)</li>
                    <li>Stripe &amp; PayPal</li>
                    <li>Paiement à la livraison</li>
                </ul>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest2 text-stone">Livraison</p>
                <p class="mt-4 text-xs text-ink/80">Partout au Maroc — plus de 25 villes.</p>
            </div>
        </div>
        <div class="border-t border-line px-4 py-5 text-center text-[10px] uppercase tracking-widest2 text-stone">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </footer>

    <x-quick-view-modal />
</body>
</html>
