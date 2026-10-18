@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Admin {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen bg-paper text-ink">
    <aside class="w-64 shrink-0 border-r border-black/10 bg-ink text-paper">
        <div class="px-6 py-6 text-lg tracking-widest2">{{ strtoupper(config('app.name')) }}</div>
        <nav class="mt-4 space-y-1 px-3 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="block rounded px-3 py-2 hover:bg-paper/10 {{ request()->routeIs('admin.dashboard') ? 'bg-paper/10' : '' }}">Tableau de bord</a>
            <a href="{{ route('admin.products.index') }}" class="block rounded px-3 py-2 hover:bg-paper/10 {{ request()->routeIs('admin.products.*') ? 'bg-paper/10' : '' }}">Produits</a>
            <a href="{{ route('admin.orders.index') }}" class="block rounded px-3 py-2 hover:bg-paper/10 {{ request()->routeIs('admin.orders.*') ? 'bg-paper/10' : '' }}">Commandes</a>
            <a href="{{ route('admin.shipping.index') }}" class="block rounded px-3 py-2 hover:bg-paper/10 {{ request()->routeIs('admin.shipping.*') ? 'bg-paper/10' : '' }}">Livraison</a>
            <a href="{{ route('home') }}" class="mt-6 block rounded px-3 py-2 text-paper/60 hover:bg-paper/10">&larr; Retour à la boutique</a>
        </nav>
    </aside>

    <div class="flex-1">
        <header class="flex items-center justify-between border-b border-black/10 bg-white px-8 py-4">
            <h1 class="text-xl">{{ $title }}</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-xs uppercase tracking-widest2 text-ink/60 hover:text-stone">Déconnexion</button>
            </form>
        </header>

        <main class="p-8">
            @if (session('success'))
                <div class="mb-6 border border-ink bg-sand px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-6 border border-ink bg-sand px-4 py-3 text-sm text-ink">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
