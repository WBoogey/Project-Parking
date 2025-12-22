<?php

namespace App\Infrastructure\Repository;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceId;
use App\Domain\Invoice\InvoiceRepositoryInterface;
use App\Domain\Invoice\InvoiceStatus;
use App\Domain\Invoice\InvoiceType;
use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use DateTime;
use PDO;

class InvoiceRepositorySQL implements InvoiceRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Invoice $invoice): void
  {
    $sql = "SELECT COUNT(*) FROM invoices WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $invoice->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE invoices SET
                invoice_number = :invoice_number,
                user_id = :user_id,
                parking_id = :parking_id,
                type = :type,
                reference_id = :reference_id,
                amount = :amount,
                currency = :currency,
                status = :status,
                description = :description,
                issued_at = :issued_at,
                paid_at = :paid_at
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO invoices (id, invoice_number, user_id, parking_id, type, reference_id, amount, currency, status, description, issued_at, paid_at)
              VALUES (:id, :invoice_number, :user_id, :parking_id, :type, :reference_id, :amount, :currency, :status, :description, :issued_at, :paid_at)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $invoice->getId()->toString(),
      ":invoice_number" => $invoice->getInvoiceNumber(),
      ":user_id" => $invoice->getUserId()->toString(),
      ":parking_id" => $invoice->getParkingId()->toString(),
      ":type" => $invoice->getType()->value,
      ":reference_id" => $invoice->getReferenceId(),
      ":amount" => $invoice->getAmount(),
      ":currency" => $invoice->getCurrency(),
      ":status" => $invoice->getStatus()->value,
      ":description" => $invoice->getDescription(),
      ":issued_at" => $invoice->getIssuedAt()->format("Y-m-d H:i:s"),
      ":paid_at" => $invoice->getPaidAt()?->format("Y-m-d H:i:s"),
    ]);
  }

  public function findById(InvoiceId $id): ?Invoice
  {
    $sql = "SELECT id, invoice_number, user_id, parking_id, type, reference_id, amount, currency, status, description, issued_at, paid_at
            FROM invoices
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateInvoice($data);
  }

  /**
   * @return Invoice[]
   */
  public function findByUserId(UserId $userId): array
  {
    $sql = "SELECT id, invoice_number, user_id, parking_id, type, reference_id, amount, currency, status, description, issued_at, paid_at
            FROM invoices
            WHERE user_id = :user_id
            ORDER BY issued_at DESC";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateInvoice($data),
      $results,
    );
  }

  public function findByReference(InvoiceType $type, string $referenceId): ?Invoice
  {
    $sql = "SELECT id, invoice_number, user_id, parking_id, type, reference_id, amount, currency, status, description, issued_at, paid_at
            FROM invoices
            WHERE type = :type AND reference_id = :reference_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":type" => $type->value,
      ":reference_id" => $referenceId,
    ]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateInvoice($data);
  }

  public function generateInvoiceNumber(): string
  {
    $year = date('Y');
    $month = date('m');
    
    // Get the last invoice number for this month
    $sql = "SELECT invoice_number FROM invoices 
            WHERE invoice_number LIKE :prefix 
            ORDER BY invoice_number DESC 
            LIMIT 1";
    $prefix = "INV-{$year}{$month}-";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":prefix" => $prefix . '%']);
    $lastNumber = $stmt->fetchColumn();

    if ($lastNumber) {
      $parts = explode('-', $lastNumber);
      $sequence = (int) end($parts) + 1;
    } else {
      $sequence = 1;
    }

    return sprintf("%s%04d", $prefix, $sequence);
  }

  public function delete(Invoice $invoice): void
  {
    $sql = "DELETE FROM invoices WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $invoice->getId()->toString()]);
  }

  private function hydrateInvoice(array $data): Invoice
  {
    return Invoice::create(
      invoiceNumber: $data["invoice_number"],
      userId: UserId::fromString($data["user_id"]),
      parkingId: ParkingId::fromString($data["parking_id"]),
      type: InvoiceType::from($data["type"]),
      referenceId: $data["reference_id"],
      amount: (int) $data["amount"],
      description: $data["description"],
      currency: $data["currency"],
      status: InvoiceStatus::from($data["status"]),
      issuedAt: new DateTime($data["issued_at"]),
      paidAt: $data["paid_at"] ? new DateTime($data["paid_at"]) : null,
      id: InvoiceId::fromString($data["id"]),
    );
  }
}
