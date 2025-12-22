<?php

declare(strict_types=1);

namespace App\Domain\Invoice\Application;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceRepositoryInterface;
use App\Domain\Invoice\InvoiceStatus;
use App\Domain\Invoice\InvoiceType;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\User\UserId;
use DateTime;

class GenerateReservationInvoice
{
  public function __construct(
    private readonly InvoiceRepositoryInterface $invoiceRepository,
    private readonly ReservationRepositoryInterface $reservationRepository,
    private readonly ParkingRepositoryInterface $parkingRepository,
  ) {}

  /**
   * Generate an invoice for a reservation
   * 
   * @throws \InvalidArgumentException if reservation not found
   * @throws \RuntimeException if invoice already exists or reservation is free
   */
  public function execute(ReservationId $reservationId, UserId $userId): Invoice
  {
    $reservation = $this->reservationRepository->findById($reservationId);
    
    if ($reservation === null) {
      throw new \InvalidArgumentException("Reservation not found");
    }

    // Check ownership
    if (!$reservation->getUserId()->equals($userId)) {
      throw new \RuntimeException("You are not authorized to generate invoice for this reservation");
    }

    // Check if invoice already exists
    $existingInvoice = $this->invoiceRepository->findByReference(
      InvoiceType::RESERVATION,
      $reservationId->toString(),
    );
    
    if ($existingInvoice !== null) {
      throw new \RuntimeException("Invoice already exists for this reservation");
    }

    // Check if reservation is free (no invoice needed)
    if ($reservation->isFree()) {
      throw new \RuntimeException("No invoice needed for free reservation (subscription)");
    }

    // Get parking info for description
    $parking = $this->parkingRepository->findById($reservation->getParkingId());
    $parkingName = $parking?->getLocation() ?? "Parking";

    $startTime = $reservation->getStartTime();
    $endTime = $reservation->getEndTime();
    $duration = ceil(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600);

    $description = sprintf(
      "Réservation %s - %s (%dh)",
      $parkingName,
      $startTime->format('d/m/Y H:i'),
      $duration
    );

    // Generate invoice
    $invoiceNumber = $this->invoiceRepository->generateInvoiceNumber();
    
    $invoice = Invoice::create(
      invoiceNumber: $invoiceNumber,
      userId: $reservation->getUserId(),
      parkingId: $reservation->getParkingId(),
      type: InvoiceType::RESERVATION,
      referenceId: $reservationId->toString(),
      amount: $reservation->getAmount() ?? 0,
      description: $description,
      currency: 'eur',
      status: InvoiceStatus::PAID,
      issuedAt: new DateTime(),
      paidAt: new DateTime(),
    );

    $this->invoiceRepository->save($invoice);

    return $invoice;
  }
}
