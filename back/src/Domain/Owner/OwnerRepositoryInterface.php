<?php



namespace App\Domain\Owner;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;

interface OwnerRepositoryInterface
{
  /** @return Parking[] */
  public function getParkings(UserId $ownerId): array;

  public function addParking(UserId $ownerId, Parking $parking): void;

  public function removeParking(UserId $ownerId, ParkingId $parkingId): void;
}
