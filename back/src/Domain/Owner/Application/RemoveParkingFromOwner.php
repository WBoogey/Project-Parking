<?php



namespace App\Domain\Owner\Application;

use App\Domain\Owner\OwnerRepositoryInterface;
use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;

class RemoveParkingFromOwner
{
  public function __construct(
    private readonly OwnerRepositoryInterface $ownerRepository,
  ) {}

  public function execute(UserId $ownerId, ParkingId $parkingId): void
  {
    $this->ownerRepository->removeParking($ownerId, $parkingId);
  }
}
