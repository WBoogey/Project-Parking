<?php

namespace App\Domain\Reservation;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use DateTime;

interface ReservationRepositoryInterface
{
  public function save(Reservation $reservation): void;

  /**
   * Save reservation with Stripe payment information
   */
  public function saveWithPayment(
    Reservation $reservation,
    string $stripeSessionId,
    string $stripePaymentStatus,
  ): void;

  /**
   * Update payment status after Stripe webhook
   */
  public function updatePaymentStatus(
    ReservationId $reservationId,
    string $stripePaymentStatus,
    ?\DateTimeImmutable $paidAt = null,
  ): void;

  /**
   * Find reservation by Stripe session ID
   */
  public function findByStripeSessionId(string $stripeSessionId): ?Reservation;

  public function findById(ReservationId $id): ?Reservation;

  /**
   * @return Reservation[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Reservation[]
   */
  public function findByUserId(UserId $userId): array;

  /**
   * Find overlapping reservations for capacity check
   * @return Reservation[]
   */
  public function findOverlapping(ParkingId $parkingId, DateTime $startTime, DateTime $endTime): array;

  /**
   * Count confirmed reservations overlapping with a time slot
   */
  public function countOverlappingConfirmed(ParkingId $parkingId, DateTime $startTime, DateTime $endTime): int;

  public function delete(Reservation $reservation): void;

  /**
   * Get Stripe session ID for a reservation
   */
  public function getStripeSessionId(ReservationId $reservationId): ?string;

  /**
   * Update refund status
   */
  public function updateRefundStatus(ReservationId $reservationId, \DateTimeImmutable $refundedAt): void;
}
