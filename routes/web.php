<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalogue
Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::get('/boutique/{category:slug}', [ShopController::class, 'index'])->name('shop.category');
Route::get('/produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Cart (guest-friendly, session based)
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::patch('/{variantId}', [CartController::class, 'update'])->name('update');
    Route::delete('/{variantId}', [CartController::class, 'destroy'])->name('destroy');
});

// Checkout
Route::prefix('commande')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'show'])->name('show');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/confirmation/{order:reference}', [CheckoutController::class, 'success'])->name('success');
});

// Static pages
Route::get('/qui-sommes-nous', [PageController::class, 'about'])->name('pages.about');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');

// Order tracking (public, guest-friendly by reference + email)
Route::get('/suivi-commande', [OrderController::class, 'track'])->name('orders.track');
Route::get('/commande/{order:reference}/facture', [InvoiceController::class, 'download'])->name('orders.invoice');

// Live shipping cost quote used by the checkout page (Alpine.js fetch)
Route::get('/api/shipping/quote', [ShippingController::class, 'quote'])->name('api.shipping.quote');

// Product data for the storefront "quick view" overlay (no page navigation)
Route::get('/api/products/{product:slug}/quick-view', [ApiProductController::class, 'quickView'])->name('api.products.quick-view');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mes-commandes', [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/mes-commandes/{order:reference}', [OrderController::class, 'show'])->name('orders.show');
});

// Admin back-office
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except('show');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:reference}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order:reference}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/shipping', [ShippingZoneController::class, 'index'])->name('shipping.index');
    Route::get('/shipping/{shippingZone}', [ShippingZoneController::class, 'edit'])->name('shipping.edit');
    Route::patch('/shipping/{shippingZone}', [ShippingZoneController::class, 'update'])->name('shipping.update');
});

require __DIR__.'/auth.php';
