<?php

namespace App\Services;

use App\Domain\Invoice\Application\GenerateReservationInvoice;
use App\Domain\Invoice\Invoice;
use App\Domain\Parking\ParkingId;
use App\Domain\Reservation\Application\CancelReservation;
use App\Domain\Reservation\Application\CancelReservationResult;
use App\Domain\Reservation\Application\CreateReservation;
use App\Domain\Reservation\Application\CreateReservationResult;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\User\UserId;
use DateTime;

class ReservationService
{
  public function __construct(
    private readonly CreateReservation $createReservationUseCase,
    private readonly CancelReservation $cancelReservationUseCase,
    private readonly GenerateReservationInvoice $generateInvoiceUseCase,
    private readonly ReservationRepositoryInterface $reservationRepository,
  ) {}

  /**
   * Create a new reservation
   */
  public function create(
    UserId $userId,
    ParkingId $parkingId,
    DateTime $startTime,
    DateTime $endTime,
  ): CreateReservationResult {
    return $this->createReservationUseCase->execute(
      userId: $userId,
      parkingId: $parkingId,
      startTime: $startTime,
      endTime: $endTime,
    );
  }

  /**
   * Cancel a reservation (with refund if paid)
   */
  public function cancel(ReservationId $reservationId, UserId $userId): CancelReservationResult
  {
    return $this->cancelReservationUseCase->execute($reservationId, $userId);
  }

  /**
   * Get reservation by ID
   */
  public function getById(ReservationId $reservationId): ?Reservation
  {
    return $this->reservationRepository->findById($reservationId);
  }

  /**
   * Get user's reservations
   * 
   * @return Reservation[]
   */
  public function getUserReservations(UserId $userId): array
  {
    return $this->reservationRepository->findByUserId($userId);
  }

  /**
   * Generate invoice for a reservation
   */
  public function generateInvoice(ReservationId $reservationId, UserId $userId): Invoice
  {
    return $this->generateInvoiceUseCase->execute($reservationId, $userId);
  }
}
