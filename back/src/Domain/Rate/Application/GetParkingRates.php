<?php

declare(strict_types=1);

namespace App\Domain\Rate\Application;

use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\User\UserId;
use InvalidArgumentException;

final class GetParkingRates
{
    public function __construct(
        private readonly RateRepositoryInterface $rateRepository,
        private readonly ParkingRepositoryInterface $parkingRepository,
    ) {}

    /**
     * Get all rates for a parking (owner must own the parking)
     *
     * @return Rate[]
     * @throws InvalidArgumentException
     */
    public function execute(UserId $ownerId, ParkingId $parkingId): array
    {
        // Verify parking exists and belongs to owner
        $parking = $this->parkingRepository->findById($parkingId);

        if ($parking === null) {
            throw new InvalidArgumentException('Parking not found');
        }

        if (!$parking->getOwnerId()->equals($ownerId)) {
            throw new InvalidArgumentException('You do not own this parking');
        }

        return $this->rateRepository->findByParkingId($parkingId);
    }
}
