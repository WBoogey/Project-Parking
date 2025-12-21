<?php

namespace App\Domain\Payment;

class RefundResult
{
    public function __construct(
        public readonly string $refundId,
        public readonly bool $success,
        public readonly ?string $errorMessage = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
