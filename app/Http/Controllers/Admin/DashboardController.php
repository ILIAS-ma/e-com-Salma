<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'revenue_month' => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'orders_pending' => Order::where('status', 'en_attente')->count(),
            'orders_total' => Order::count(),
            'products_active' => Product::active()->count(),
            'low_stock' => ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
        ];

        $recentOrders = Order::with('city')->latest()->take(10)->get();

        $salesByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', compact('stats', 'recentOrders', 'salesByStatus'));
    }
}
