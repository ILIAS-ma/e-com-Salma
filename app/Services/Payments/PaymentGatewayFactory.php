<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGatewayInterface
    {
        return match ($method) {
            'stripe' => app(StripeGateway::class),
            'paypal' => app(PayPalGateway::class),
            'cmi' => app(CmiGateway::class),
            'cod' => app(CashOnDeliveryGateway::class),
            default => throw new InvalidArgumentException("Unknown payment method [{$method}]."),
        };
    }
}
