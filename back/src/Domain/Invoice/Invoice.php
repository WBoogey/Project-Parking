<?php

namespace App\Domain\Invoice;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;
use DateTime;

/**
 * Invoice for a parking-related payment
 * 
 * @extends Entity<array{id: InvoiceId, invoiceNumber: string, userId: UserId, parkingId: ParkingId, type: InvoiceType, referenceId: string, amount: int, currency: string, status: InvoiceStatus, description: string, issuedAt: DateTime, paidAt: ?DateTime}>
 */
class Invoice extends Entity
{
  private function __construct(
    InvoiceId $id,
    string $invoiceNumber,
    UserId $userId,
    ParkingId $parkingId,
    InvoiceType $type,
    string $referenceId,
    int $amount,
    string $currency,
    InvoiceStatus $status,
    string $description,
    DateTime $issuedAt,
    ?DateTime $paidAt,
  ) {
    parent::__construct([
      "id" => $id,
      "invoiceNumber" => $invoiceNumber,
      "userId" => $userId,
      "parkingId" => $parkingId,
      "type" => $type,
      "referenceId" => $referenceId,
      "amount" => $amount,
      "currency" => $currency,
      "status" => $status,
      "description" => $description,
      "issuedAt" => $issuedAt,
      "paidAt" => $paidAt,
    ]);
  }

  public static function create(
    string $invoiceNumber,
    UserId $userId,
    ParkingId $parkingId,
    InvoiceType $type,
    string $referenceId,
    int $amount,
    string $description,
    string $currency = 'eur',
    InvoiceStatus $status = InvoiceStatus::ISSUED,
    ?DateTime $issuedAt = null,
    ?DateTime $paidAt = null,
    ?InvoiceId $id = null,
  ): self {
    return new self(
      id: $id ?? InvoiceId::generate(),
      invoiceNumber: $invoiceNumber,
      userId: $userId,
      parkingId: $parkingId,
      type: $type,
      referenceId: $referenceId,
      amount: $amount,
      currency: $currency,
      status: $status,
      description: $description,
      issuedAt: $issuedAt ?? new DateTime(),
      paidAt: $paidAt,
    );
  }

  public function getId(): InvoiceId
  {
    return $this->props["id"];
  }

  public function getInvoiceNumber(): string
  {
    return $this->props["invoiceNumber"];
  }

  public function getUserId(): UserId
  {
    return $this->props["userId"];
  }

  public function getParkingId(): ParkingId
  {
    return $this->props["parkingId"];
  }

  public function getType(): InvoiceType
  {
    return $this->props["type"];
  }

  public function getReferenceId(): string
  {
    return $this->props["referenceId"];
  }

  public function getAmount(): int
  {
    return $this->props["amount"];
  }

  public function getCurrency(): string
  {
    return $this->props["currency"];
  }

  public function getStatus(): InvoiceStatus
  {
    return $this->props["status"];
  }

  public function getDescription(): string
  {
    return $this->props["description"];
  }

  public function getIssuedAt(): DateTime
  {
    return $this->props["issuedAt"];
  }

  public function getPaidAt(): ?DateTime
  {
    return $this->props["paidAt"];
  }

  /**
   * Get amount formatted (e.g., "3.00 EUR")
   */
  public function getFormattedAmount(): string
  {
    $amountFormatted = number_format($this->getAmount() / 100, 2, '.', '');
    return "{$amountFormatted} " . strtoupper($this->getCurrency());
  }

  /**
   * Mark invoice as paid
   */
  public function markAsPaid(DateTime $paidAt): self
  {
    return new self(
      id: $this->getId(),
      invoiceNumber: $this->getInvoiceNumber(),
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      type: $this->getType(),
      referenceId: $this->getReferenceId(),
      amount: $this->getAmount(),
      currency: $this->getCurrency(),
      status: InvoiceStatus::PAID,
      description: $this->getDescription(),
      issuedAt: $this->getIssuedAt(),
      paidAt: $paidAt,
    );
  }
}
