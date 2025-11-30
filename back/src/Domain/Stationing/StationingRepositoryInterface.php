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

  public function delete(Stationing $stationing): void;
}
