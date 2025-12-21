<?php

declare(strict_types=1);

namespace App\Domain\Rate\Application;

use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\User\UserId;
use InvalidArgumentException;

final class DeleteRate
{
    public function __construct(
        private readonly RateRepositoryInterface $rateRepository,
        private readonly ParkingRepositoryInterface $parkingRepository,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function execute(UserId $ownerId, RateId $rateId): void
    {
        // Find the rate
        $rate = $this->rateRepository->findById($rateId);

        if ($rate === null) {
            throw new InvalidArgumentException('Rate not found');
        }

        // Verify parking belongs to owner
        $parking = $this->parkingRepository->findById($rate->getParkingId());

        if ($parking === null) {
            throw new InvalidArgumentException('Parking not found');
        }

        if (!$parking->getOwnerId()->equals($ownerId)) {
            throw new InvalidArgumentException('You do not own this parking');
        }

        $this->rateRepository->delete($rate);
    }
}
