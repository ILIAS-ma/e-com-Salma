<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        abort_unless(
            $order->payment_status === 'paid' || $order->payment_method === 'cod',
            403,
            'Facture indisponible pour cette commande.'
        );

        $order->load('items', 'city.shippingZone');

        $pdf = Pdf::loadView('invoices.order', compact('order'));

        return $pdf->download("facture-{$order->reference}.pdf");
    }
}
