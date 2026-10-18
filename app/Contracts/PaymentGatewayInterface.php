<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment for the given order.
     * Returns an array with at least a 'redirect_url' (for hosted checkouts)
     * or 'status' when the payment is resolved synchronously (e.g. Cash on Delivery).
     */
    public function charge(Order $order): array;

    /**
     * Confirm/verify a payment coming back from the gateway (webhook or redirect callback).
     */
    public function confirm(Order $order, array $payload): Payment;

    public function name(): string;
}
