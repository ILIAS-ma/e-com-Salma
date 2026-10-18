<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::active()->featured()->with('images')->latest('published_at')->take(8)->get();
        $newArrivals = Product::active()->with('images')
            ->whereNotIn('id', $featured->pluck('id'))
            ->latest('published_at')->take(8)->get();
        $categories = Category::active()->roots()->orderBy('position')->take(5)->get();

        $topFaqs = collect(config('faq'))->collapse()->take(3);

        return view('home', compact('featured', 'newArrivals', 'categories', 'topFaqs'));
    }
}
