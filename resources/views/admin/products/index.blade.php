<x-admin-layout title="Produits">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.products.create') }}" class="bg-ink px-6 py-2 text-xs uppercase tracking-widest2 text-paper">Nouveau produit</a>
    </div>

    <div class="border border-black/10 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-black/10 text-xs uppercase tracking-widest2 text-ink/40">
                <tr>
                    <th class="p-4">Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($products as $product)
                    <tr>
                        <td class="p-4">{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ number_format($product->price, 2) }} MAD</td>
                        <td>{{ $product->totalStock() }}</td>
                        <td>
                            <span class="rounded-full px-2 py-1 text-xs {{ $product->is_active ? 'bg-sand text-ink' : 'bg-black/5 text-ink/50' }}">
                                {{ $product->is_active ? 'Actif' : 'Brouillon' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-stone hover:underline">Modifier</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button class="ml-3 text-ink/40 hover:text-stone">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
