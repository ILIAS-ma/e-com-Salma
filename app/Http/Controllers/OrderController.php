<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Public order tracking: identified by reference + email, no login required
     * since Cash on Delivery customers frequently check out as guests.
     */
    public function track(Request $request)
    {
        $order = null;

        if ($request->filled(['reference', 'email'])) {
            $order = Order::with('items')
                ->where('reference', $request->string('reference'))
                ->where('email', $request->string('email'))
                ->first();

            if (! $order) {
                return back()->with('error', 'Aucune commande trouvée avec ces informations.');
            }
        }

        return view('orders.track', compact('order'));
    }

    public function myOrders(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items', 'city', 'shippingZone');

        return view('orders.show', compact('order'));
    }
}
