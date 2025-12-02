<?php

namespace App\Services;

use App\Domain\Owner\Application\GetOwnerParkings;
use App\Domain\Owner\Application\AddParkingToOwner;
use App\Domain\Owner\Application\RemoveParkingFromOwner;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;

class OwnerService
{
    public function __construct(
        private readonly GetOwnerParkings $getOwnerParkingsUseCase,
        private readonly AddParkingToOwner $addParkingToOwnerUseCase,
        private readonly RemoveParkingFromOwner $removeParkingFromOwnerUseCase,
    ) {}

    /** @return Parking[] */
    public function getParkings(UserId $ownerId): array
    {
        return $this->getOwnerParkingsUseCase->execute($ownerId);
    }

    public function addParking(UserId $ownerId, Parking $parking): void
    {
        $this->addParkingToOwnerUseCase->execute($ownerId, $parking);
    }

    public function removeParking(UserId $ownerId, ParkingId $parkingId): void
    {
        $this->removeParkingFromOwnerUseCase->execute($ownerId, $parkingId);
    }
}
