<?php



namespace App\Domain\Owner\Application;

use App\Domain\Owner\OwnerRepositoryInterface;
use App\Domain\Parking\Parking;
use App\Domain\User\UserId;

class AddParkingToOwner
{
  public function __construct(
    private readonly OwnerRepositoryInterface $ownerRepository,
  ) {}

  public function execute(UserId $ownerId, Parking $parking): void
  {
    $this->ownerRepository->addParking($ownerId, $parking);
  }
}
