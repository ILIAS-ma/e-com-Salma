<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

/**
 * Talks directly to the PayPal Orders API v2 (REST) since PayPal's official SDKs
 * are unmaintained. MAD is not settled by PayPal, so the order total is converted
 * to USD using the fixed rate in config('services.paypal.mad_to_usd_rate').
 */
class PayPalGateway implements PaymentGatewayInterface
{
    protected function baseUrl(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    protected function accessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            ->post($this->baseUrl().'/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        return $response->throw()->json('access_token');
    }

    protected function amountInUsd(Order $order): string
    {
        $rate = (float) config('services.paypal.mad_to_usd_rate', 0.1);

        return number_format((float) $order->total * $rate, 2, '.', '');
    }

    public function charge(Order $order): array
    {
        $token = $this->accessToken();

        $response = Http::withToken($token)->post($this->baseUrl().'/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $order->reference,
                'amount' => ['currency_code' => 'USD', 'value' => $this->amountInUsd($order)],
            ]],
            'application_context' => [
                'return_url' => route('checkout.success', $order->reference),
                'cancel_url' => route('checkout.show'),
            ],
        ])->throw()->json();

        Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->name(),
            'transaction_id' => $response['id'],
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        $approveUrl = collect($response['links'])->firstWhere('rel', 'approve')['href'] ?? null;

        return ['status' => 'pending', 'redirect_url' => $approveUrl];
    }

    public function confirm(Order $order, array $payload): Payment
    {
        $payment = $order->payments()->where('gateway', $this->name())->latest()->firstOrFail();

        $token = $this->accessToken();
        $response = Http::withToken($token)
            ->post($this->baseUrl()."/v2/checkout/orders/{$payment->transaction_id}/capture")
            ->throw()->json();

        $payment->update([
            'status' => ($response['status'] ?? null) === 'COMPLETED' ? 'succeeded' : 'failed',
            'gateway_response' => $response,
        ]);

        return $payment;
    }

    public function name(): string
    {
        return 'paypal';
    }
}
