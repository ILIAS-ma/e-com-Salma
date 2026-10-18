<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::whereNotNull('parent_id')->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($request->safe()->except(['variants', 'images', 'delete_images', 'primary_image']) + [
                'is_active' => $request->boolean('is_active'),
                'is_featured' => $request->boolean('is_featured'),
                'published_at' => $request->boolean('is_active') ? now() : null,
            ]);

            foreach ($request->input('variants') as $variant) {
                $product->variants()->create($variant);
            }

            $this->syncImages($product, $request);

            return $product;
        });

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product)
    {
        $product->load('variants', 'images');
        $categories = Category::whereNotNull('parent_id')->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->safe()->except(['variants', 'images', 'delete_images', 'primary_image']) + [
                'is_active' => $request->boolean('is_active'),
                'is_featured' => $request->boolean('is_featured'),
                'published_at' => $request->boolean('is_active') ? ($product->published_at ?? now()) : $product->published_at,
            ]);

            $submittedIds = collect($request->input('variants'))->pluck('id')->filter();
            $product->variants()->whereNotIn('id', $submittedIds)->delete();

            foreach ($request->input('variants') as $variant) {
                $product->variants()->updateOrCreate(['id' => $variant['id'] ?? null], $variant);
            }

            $this->syncImages($product, $request);
        });

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            $this->deleteImageFile($image->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé.');
    }

    /**
     * Handle image deletions, new uploads and the primary image flag for a product.
     */
    protected function syncImages(Product $product, ProductRequest $request): void
    {
        $deleteIds = collect($request->input('delete_images', []));

        if ($deleteIds->isNotEmpty()) {
            $images = $product->images()->whereIn('id', $deleteIds)->get();

            foreach ($images as $image) {
                $this->deleteImageFile($image->path);
                $image->delete();
            }
        }

        if ($request->hasFile('images')) {
            $position = $product->images()->max('position') + 1;

            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');

                $product->images()->create([
                    'path' => $path,
                    'position' => $position++,
                    'is_primary' => false,
                ]);
            }
        }

        $primaryId = $request->input('primary_image');

        if ($primaryId && $product->images()->whereKey($primaryId)->exists()) {
            $product->images()->update(['is_primary' => false]);
            $product->images()->whereKey($primaryId)->update(['is_primary' => true]);
        } elseif (! $product->images()->where('is_primary', true)->exists()) {
            $product->images()->oldest('position')->limit(1)->update(['is_primary' => true]);
        }
    }

    protected function deleteImageFile(string $path): void
    {
        if (! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
