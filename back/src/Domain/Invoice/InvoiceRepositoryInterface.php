<?php

namespace App\Domain\Invoice;

use App\Domain\User\UserId;

interface InvoiceRepositoryInterface
{
  public function save(Invoice $invoice): void;

  public function findById(InvoiceId $id): ?Invoice;

  /**
   * @return Invoice[]
   */
  public function findByUserId(UserId $userId): array;

  /**
   * Find invoice by reference (reservation_id, stationing_id, subscription_id)
   */
  public function findByReference(InvoiceType $type, string $referenceId): ?Invoice;

  /**
   * Generate next invoice number
   */
  public function generateInvoiceNumber(): string;

  public function delete(Invoice $invoice): void;
}
