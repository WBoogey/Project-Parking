<?php



namespace App\Domain\Owner\Application;

use App\Domain\Owner\OwnerRepositoryInterface;
use App\Domain\Parking\Parking;
use App\Domain\User\UserId;

class GetOwnerParkings
{
  public function __construct(
    private readonly OwnerRepositoryInterface $ownerRepository,
  ) {}

  /** @return Parking[] */
  public function execute(UserId $ownerId): array
  {
    return $this->ownerRepository->getParkings($ownerId);
  }
}
