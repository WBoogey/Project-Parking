<?php

namespace App\Domain\Payment;

class CustomerData
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly ?string $phone = null,
        public readonly ?string $userId = null,
    ) {}
}
