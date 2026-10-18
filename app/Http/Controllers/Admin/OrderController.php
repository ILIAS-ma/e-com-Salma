<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('city')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($q) => $q->where('reference', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'city.shippingZone', 'payments', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:en_attente,confirmee,en_preparation,en_livraison,livree,annulee'],
        ]);

        $timestampField = match ($data['status']) {
            'confirmee' => 'confirmed_at',
            'en_livraison' => 'shipped_at',
            'livree' => 'delivered_at',
            'annulee' => 'cancelled_at',
            default => null,
        };

        $order->update([
            'status' => $data['status'],
            ...($timestampField ? [$timestampField => now()] : []),
            ...($data['status'] === 'livree' && $order->payment_method === 'cod' ? ['payment_status' => 'paid'] : []),
        ]);

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}
