<?php



namespace App\Domain\Parking;

use App\Domain\User\UserId;

interface ParkingRepositoryInterface
{
  public function save(Parking $parking): void;

  public function findById(ParkingId $id): ?Parking;

  public function findByLocation(string $location): ?Parking;

  /**
   * @return Parking[]
   */
  public function findByOwnerId(UserId $ownerId): array;

  public function delete(Parking $parking): void;
}
