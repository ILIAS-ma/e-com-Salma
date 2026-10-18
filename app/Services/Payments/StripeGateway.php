<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeGateway implements PaymentGatewayInterface
{
    protected StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function charge(Order $order): array
    {
        $session = $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'customer_email' => $order->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => ['name' => 'Commande '.$order->reference],
                    'unit_amount' => (int) round($order->total * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => ['order_reference' => $order->reference],
            'success_url' => route('checkout.success', $order->reference).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.show'),
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->name(),
            'transaction_id' => $session->id,
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return ['status' => 'pending', 'redirect_url' => $session->url];
    }

    public function confirm(Order $order, array $payload): Payment
    {
        $sessionId = $payload['session_id'] ?? null;
        $session = $sessionId ? Session::retrieve($sessionId, ['api_key' => config('services.stripe.secret')]) : null;

        $payment = $order->payments()->where('gateway', $this->name())->latest()->firstOrFail();
        $payment->update([
            'status' => $session && $session->payment_status === 'paid' ? 'succeeded' : 'failed',
            'gateway_response' => $payload,
        ]);

        return $payment;
    }

    public function name(): string
    {
        return 'stripe';
    }
}
