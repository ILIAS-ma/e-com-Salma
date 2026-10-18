<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Serialized product payload for the storefront's "quick view" overlay,
     * so a product can be previewed without leaving the current page.
     */
    public function quickView(Product $product)
    {
        $product->load('images', 'variants');

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'collection' => $product->collection,
            'description' => $product->description,
            'composition' => $product->composition,
            'care_instructions' => $product->care_instructions,
            'price' => (float) $product->price,
            'compare_at_price' => $product->compare_at_price ? (float) $product->compare_at_price : null,
            'url' => route('products.show', $product),
            'images' => $product->images->map(fn ($image) => $image->url)->values(),
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'size' => $variant->size,
                'color_name' => $variant->color_name,
                'color_code' => $variant->color_code,
                'stock' => $variant->stock,
            ])->values(),
        ]);
    }
}
