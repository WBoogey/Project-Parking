<?php

namespace App\Domain\Payment;

class PaymentRequest
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly ?string $customerId = null,
        public readonly array $metadata = [],
        public readonly ?string $successUrl = null,
        public readonly ?string $cancelUrl = null,
    ) {}
}
