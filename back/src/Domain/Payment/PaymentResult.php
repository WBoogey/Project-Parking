<?php

namespace App\Domain\Payment;

class PaymentResult
{
    public function __construct(
        public readonly string $paymentId,
        public readonly PaymentStatus $status,
        public readonly ?string $checkoutUrl = null,
        public readonly ?string $errorMessage = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }
}
