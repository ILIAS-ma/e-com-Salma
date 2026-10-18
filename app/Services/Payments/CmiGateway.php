<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;

/**
 * CMI (Centre Monétique Interbancaire) is the payment processor used by most
 * Moroccan banks. Integration is a hosted-page redirect signed with HMAC-SHA512,
 * per CMI's merchant integration guide. There is no public Composer SDK, so the
 * request fields and hash are built manually here.
 */
class CmiGateway implements PaymentGatewayInterface
{
    public function charge(Order $order): array
    {
        $fields = [
            'clientid' => config('services.cmi.merchant_id'),
            'oid' => $order->reference,
            'amount' => number_format((float) $order->total, 2, '.', ''),
            'currency' => '504', // ISO 4217 numeric code for MAD
            'okUrl' => route('checkout.success', $order->reference),
            'failUrl' => route('checkout.show'),
            'email' => $order->email,
            'BillToName' => $order->full_name,
            'rnd' => (string) microtime(true),
            'storetype' => '3d_pay_hosting',
            'lang' => 'fr',
            'hashAlgorithm' => 'ver3',
        ];

        $fields['hash'] = $this->generateHash($fields);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->name(),
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        // CMI expects an auto-submitted POST form to its hosted payment page,
        // so the checkout view renders `fields` inside a form rather than a simple redirect.
        return [
            'status' => 'pending',
            'redirect_url' => config('services.cmi.gateway_url'),
            'fields' => $fields,
        ];
    }

    protected function generateHash(array $fields): string
    {
        $storeKey = config('services.cmi.store_key');
        $order = ['clientid', 'oid', 'amount', 'okUrl', 'failUrl', 'rnd'];

        $hashString = collect($order)
            ->map(fn ($key) => str_replace(['\\', '|'], ['\\\\', '\\|'], $fields[$key]))
            ->implode('|').'|'.$storeKey;

        return base64_encode(hash('sha512', $hashString, true));
    }

    public function confirm(Order $order, array $payload): Payment
    {
        $payment = $order->payments()->where('gateway', $this->name())->latest()->firstOrFail();
        $payment->update([
            'status' => ($payload['Response'] ?? null) === 'Approved' ? 'succeeded' : 'failed',
            'transaction_id' => $payload['TransId'] ?? null,
            'gateway_response' => $payload,
        ]);

        return $payment;
    }

    public function name(): string
    {
        return 'cmi';
    }
}
