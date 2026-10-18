@props(['maxWidth' => 'sm'])

@php
$widthClasses = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    default => 'max-w-sm',
};
@endphp
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} — Silk & More</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <script>document.documentElement.classList.add('js');</script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-paper text-ink antialiased">
        <header class="relative border-b border-line px-4 py-6 text-center sm:px-6">
            <a href="{{ route('shop.index') }}" class="absolute left-4 top-1/2 -translate-y-1/2 hidden text-[11px] uppercase tracking-widest2 text-stone hover:text-ink transition-colors sm:inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Boutique
            </a>
            <a href="{{ route('home') }}" class="inline-block transition-transform duration-500 ease-silk hover:scale-[1.03]">
                <x-application-logo class="h-14 w-auto" />
            </a>
        </header>

        <main class="flex flex-1 items-center justify-center px-4 py-10 sm:py-16">
            <div class="w-full {{ $widthClasses }}">
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-line py-6 text-center text-[11px] uppercase tracking-widest2 text-stone">
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                <a href="{{ route('shop.index') }}" class="hover:text-ink transition-colors">Boutique</a>
                <span class="opacity-40">&middot;</span>
                <a href="{{ route('orders.track') }}" class="hover:text-ink transition-colors">Suivi de commande</a>
                <span class="opacity-40">&middot;</span>
                <a href="{{ route('pages.faq') }}" class="hover:text-ink transition-colors">Aide &amp; FAQ</a>
            </div>
        </footer>
    </body>
</html>
