<?php

namespace App\Domain\Stationing;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use DateTime;

interface StationingRepositoryInterface
{
  public function save(Stationing $stationing): void;

  public function findById(StationingId $id): ?Stationing;

  /**
   * @return Stationing[]
   */
  public function findByInterval(DateTime $startTime, DateTime $endTime): array;

  /**
   * @return Stationing[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Stationing[]
   */
  public function findByUserId(UserId $userId): array;

  /**
   * Find active stationing for a user in a specific parking
   */
  public function findActiveByUserAndParking(UserId $userId, ParkingId $parkingId): ?Stationing;

  /**
   * Count active stationings in a parking (to check capacity)
   */
  public function countActiveByParkingId(ParkingId $parkingId): int;

  public function delete(Stationing $stationing): void;

  /**
   * Save stationing with Stripe payment information
   */
  public function saveWithPayment(
    Stationing $stationing,
    string $stripeSessionId,
    string $stripePaymentStatus,
  ): void;

  /**
   * Update payment status after Stripe webhook
   */
  public function updatePaymentStatus(
    StationingId $stationingId,
    string $stripePaymentStatus,
    ?\DateTimeImmutable $paidAt = null,
  ): void;

  /**
   * Find stationing by Stripe session ID
   */
  public function findByStripeSessionId(string $stripeSessionId): ?Stationing;
}
