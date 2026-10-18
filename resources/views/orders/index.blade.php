<x-shop-layout title="Mes commandes">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-lg uppercase tracking-widest2">Mes commandes</h1>

        <div class="mt-8 divide-y divide-line border-y border-line">
            @forelse ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between py-4 text-sm hover:bg-sand/40">
                    <div>
                        <p class="font-medium">{{ $order->reference }}</p>
                        <p class="text-stone">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p>{{ $order->statusLabel() }}</p>
                        <p class="text-stone">{{ number_format($order->total, 2) }} MAD</p>
                    </div>
                </a>
            @empty
                <p class="py-10 text-center text-stone">Vous n'avez pas encore de commande.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    </div>
</x-shop-layout>
