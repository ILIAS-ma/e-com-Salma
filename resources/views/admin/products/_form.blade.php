@php
    $existingVariants = ($product ?? null)?->variants->map(fn ($v) => [
        'id' => $v->id, 'size' => $v->size, 'color_name' => $v->color_name,
        'color_code' => $v->color_code, 'stock' => $v->stock, 'sku' => $v->sku,
    ])->values() ?? collect();
@endphp

<div
    x-data="{
        variants: {{ Js::from($existingVariants->isEmpty() ? [['id' => null, 'size' => '', 'color_name' => '', 'color_code' => '#C1440E', 'stock' => 0, 'sku' => '']] : $existingVariants) }},
        addVariant() {
            this.variants.push({ id: null, size: '', color_name: '', color_code: '#C1440E', stock: 0, sku: '' });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        },
    }"
>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Informations générales</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="block text-sm sm:col-span-2">
                        Nom du produit
                        <input name="name" value="{{ old('name', $product->name ?? '') }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
                    </label>
                    <label class="block text-sm sm:col-span-2">
                        Slug (URL)
                        <input name="slug" value="{{ old('slug', $product->slug ?? '') }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
                    </label>
                    <label class="block text-sm sm:col-span-2">
                        Description
                        <textarea name="description" rows="4" class="mt-1 w-full rounded-none border-black/20 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                    </label>
                    <label class="block text-sm">
                        Composition
                        <input name="composition" value="{{ old('composition', $product->composition ?? '') }}" class="mt-1 w-full rounded-none border-black/20 text-sm">
                    </label>
                    <label class="block text-sm">
                        Entretien
                        <input name="care_instructions" value="{{ old('care_instructions', $product->care_instructions ?? '') }}" class="mt-1 w-full rounded-none border-black/20 text-sm">
                    </label>
                </div>
            </div>

            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Images</h2>

                @if (($product?->images ?? collect())->isNotEmpty())
                    <div class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($product->images as $image)
                            <div class="relative border border-black/10 p-2 text-center">
                                <img src="{{ $image->url }}" alt="" class="aspect-square w-full object-cover">
                                <label class="mt-2 flex items-center justify-center gap-1 text-[11px] text-stone">
                                    <input type="radio" name="primary_image" value="{{ $image->id }}" @checked($image->is_primary)>
                                    Principale
                                </label>
                                <label class="mt-1 flex items-center justify-center gap-1 text-[11px] text-stone">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                                    Supprimer
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label class="block text-sm">
                    Ajouter des images
                    <input type="file" name="images[]" multiple accept="image/*" class="mt-1 w-full rounded-none border-black/20 text-sm">
                </label>
                @error('images.*')
                    <p class="mt-1 text-xs text-ink">{{ $message }}</p>
                @enderror
            </div>

            <div class="border border-black/10 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-medium">Variantes (taille / couleur / stock)</h2>
                    <button type="button" @click="addVariant()" class="text-xs uppercase tracking-widest2 text-stone">+ Ajouter</button>
                </div>

                <template x-for="(variant, index) in variants" :key="index">
                    <div class="mb-3 grid grid-cols-12 gap-2 border-b border-black/5 pb-3">
                        <input type="hidden" :name="`variants[${index}][id]`" x-model="variant.id">
                        <input :name="`variants[${index}][size]`" x-model="variant.size" placeholder="Taille" class="col-span-2 rounded-none border-black/20 text-xs">
                        <input :name="`variants[${index}][color_name]`" x-model="variant.color_name" placeholder="Couleur" class="col-span-3 rounded-none border-black/20 text-xs">
                        <input type="color" :name="`variants[${index}][color_code]`" x-model="variant.color_code" class="col-span-1 h-9 rounded-none border-black/20">
                        <input :name="`variants[${index}][sku]`" x-model="variant.sku" placeholder="SKU" class="col-span-3 rounded-none border-black/20 text-xs">
                        <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" placeholder="Stock" min="0" class="col-span-2 rounded-none border-black/20 text-xs">
                        <button type="button" @click="removeVariant(index)" class="col-span-1 text-ink/40 hover:text-stone">&times;</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="space-y-4">
            <div class="border border-black/10 bg-white p-6">
                <h2 class="mb-4 text-sm font-medium">Prix &amp; classement</h2>
                <label class="block text-sm">
                    Prix (MAD)
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required class="mt-1 w-full rounded-none border-black/20 text-sm">
                </label>
                <label class="mt-3 block text-sm">
                    Prix barré (optionnel)
                    <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}" class="mt-1 w-full rounded-none border-black/20 text-sm">
                </label>
                <label class="mt-3 block text-sm">
                    Catégorie
                    <select name="category_id" required class="mt-1 w-full rounded-none border-black/20 text-sm">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? null) == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="mt-3 block text-sm">
                    Genre
                    <select name="gender" class="mt-1 w-full rounded-none border-black/20 text-sm">
                        @foreach (['femme' => 'Femme', 'homme' => 'Homme', 'enfant' => 'Enfant', 'mixte' => 'Mixte'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('gender', $product->gender ?? 'mixte') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="mt-3 block text-sm">
                    Collection
                    <input name="collection" value="{{ old('collection', $product->collection ?? '') }}" class="mt-1 w-full rounded-none border-black/20 text-sm">
                </label>
                <label class="mt-4 flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                    Publié
                </label>
                <label class="mt-2 flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))>
                    Mis en avant
                </label>
            </div>

            <button type="submit" class="w-full bg-ink py-3 text-xs uppercase tracking-widest2 text-paper">Enregistrer</button>
        </div>
    </div>
</div>
