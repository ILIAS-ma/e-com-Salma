<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;

/**
 * Cash on Delivery is resolved synchronously: no external redirect is needed,
 * the order is simply marked as pending payment until delivery is confirmed.
 */
class CashOnDeliveryGateway implements PaymentGatewayInterface
{
    public function charge(Order $order): array
    {
        Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->name(),
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return ['status' => 'pending', 'redirect_url' => null];
    }

    public function confirm(Order $order, array $payload): Payment
    {
        $payment = $order->payments()->where('gateway', $this->name())->latest()->firstOrFail();
        $payment->update(['status' => 'succeeded', 'gateway_response' => $payload]);

        return $payment;
    }

    public function name(): string
    {
        return 'cod';
    }
}
