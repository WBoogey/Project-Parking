<?php

declare(strict_types=1);

namespace App\Domain\Parking\Application;

use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;

class UpdateParking
{
    public function __construct(
        private readonly ParkingRepositoryInterface $parkingRepository,
    ) {}

    /**
     * @throws \InvalidArgumentException if parking not found or not owned by user
     */
    public function execute(
        string $parkingId,
        string $ownerId,
        ?string $location = null,
        ?int $capacity = null,
    ): Parking {
        $parking = $this->parkingRepository->findById(ParkingId::fromString($parkingId));

        if ($parking === null) {
            throw new \InvalidArgumentException('Parking not found');
        }

        // Verify ownership
        if ($parking->getOwnerId()->toString() !== $ownerId) {
            throw new \InvalidArgumentException('You do not own this parking');
        }

        // Create updated parking
        $updatedParking = Parking::create(
            location: $location ?? $parking->getLocation(),
            capacity: $capacity ?? $parking->getCapacity(),
            ownerId: $parking->getOwnerId(),
            id: $parking->getId(),
        );

        $this->parkingRepository->save($updatedParking);

        return $updatedParking;
    }
}
