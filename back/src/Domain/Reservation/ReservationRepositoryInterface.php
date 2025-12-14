<?php



namespace App\Domain\Reservation;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\Rate;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\User\UserId;

interface ReservationRepositoryInterface
{
  public function save(Reservation $reservation): void;

  public function findById(ReservationId $id): ?Reservation;

  /**
   * @return Reservation[]
   */
  public function findByInterval(TimeInterval $interval): array;

  /**
   * @return Reservation[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Reservation[]
   */
  public function findByUserId(UserId $userId): array;

  public function getRateForReservation(Reservation $reservation): ?Rate;

  public function delete(Reservation $reservation): void;
}
