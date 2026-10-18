<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $order->reference }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1a1a1a; }
        h1 { font-size: 20px; letter-spacing: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e5e5; }
        .totals td { border: none; }
        .text-right { text-align: right; }
        .muted { color: #777; }
    </style>
</head>
<body>
    <h1>{{ strtoupper(config('app.name')) }}</h1>
    <p class="muted">Facture — Commande {{ $order->reference }}</p>
    <p class="muted">{{ $order->created_at->format('d/m/Y') }}</p>

    <table style="margin-top: 30px;">
        <tr>
            <td style="border: none; width: 50%;">
                <strong>Facturé à</strong><br>
                {{ $order->full_name }}<br>
                {{ $order->address }}<br>
                {{ $order->city->name }}, {{ $order->postal_code }}<br>
                {{ $order->email }} — {{ $order->phone }}
            </td>
            <td style="border: none; width: 50%;">
                <strong>Détails de paiement</strong><br>
                Méthode : {{ strtoupper($order->payment_method) }}<br>
                Statut : {{ $order->payment_status }}<br>
                Zone de livraison : {{ $order->city->shippingZone->name }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Taille / Couleur</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->size }} / {{ $item->color_name }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }} MAD</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }} MAD</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals" style="width: 300px; margin-left: auto;">
        <tr><td>Sous-total</td><td class="text-right">{{ number_format($order->subtotal, 2) }} MAD</td></tr>
        <tr><td>Livraison</td><td class="text-right">{{ number_format($order->shipping_cost, 2) }} MAD</td></tr>
        <tr><td><strong>Total</strong></td><td class="text-right"><strong>{{ number_format($order->total, 2) }} MAD</strong></td></tr>
    </table>
</body>
</html>
