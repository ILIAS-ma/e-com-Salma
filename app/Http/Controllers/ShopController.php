<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, ?Category $category = null)
    {
        $query = Product::query()->active()->with(['images', 'variants']);

        if ($category) {
            $categoryIds = $category->children()->pluck('id')->push($category->id);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('gender')) {
            $query->gender($request->string('gender'));
        }

        if ($request->filled('collection')) {
            $query->where('collection', $request->string('collection'));
        }

        if ($request->boolean('sale')) {
            $query->whereNotNull('compare_at_price')->whereColumn('compare_at_price', '>', 'price');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('size')) {
            $size = $request->string('size');
            $query->whereHas('variants', fn ($q) => $q->where('size', $size)->inStock());
        }

        if ($request->filled('color')) {
            $color = $request->string('color');
            $query->whereHas('variants', fn ($q) => $q->where('color_name', $color)->inStock());
        }

        $query->when($request->input('sort'), function ($q, $sort) {
            match ($sort) {
                'price_asc' => $q->orderBy('price'),
                'price_desc' => $q->orderByDesc('price'),
                'popularity' => $q->orderByDesc('sales_count'),
                default => $q->latest('published_at'),
            };
        }, fn ($q) => $q->latest('published_at'));

        $products = $query->paginate(24)->withQueryString();
        $categories = Category::active()->roots()->with('children')->orderBy('position')->get();

        $colors = ProductVariant::select('color_name')->distinct()->orderBy('color_name')->pluck('color_name');
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'Taille unique'];

        return view('shop.index', compact('products', 'categories', 'category', 'colors', 'sizes'));
    }
}
