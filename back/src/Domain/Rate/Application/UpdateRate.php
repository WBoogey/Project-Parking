<?php

declare(strict_types=1);

namespace App\Domain\Rate\Application;

use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\User\UserId;
use InvalidArgumentException;

final class UpdateRate
{
    public function __construct(
        private readonly RateRepositoryInterface $rateRepository,
        private readonly ParkingRepositoryInterface $parkingRepository,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function execute(
        UserId $ownerId,
        RateId $rateId,
        ?float $price = null,
        ?string $calculationRule = null,
        ?float $hourlyDiscount = null,
        ?string $duration = null,
    ): Rate {
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

        // Validate price if provided
        if ($price !== null && $price <= 0) {
            throw new InvalidArgumentException('Price must be greater than 0');
        }

        // Create updated rate
        $updatedRate = Rate::create(
            parkingId: $rate->getParkingId(),
            type: $rate->getType(),
            calculationRule: $calculationRule ?? $rate->getCalculationRule(),
            price: $price ?? $rate->getPrice(),
            hourlyDiscount: $hourlyDiscount ?? $rate->getHourlyDiscount(),
            duration: $duration ?? $rate->getDuration(),
            id: $rate->getId(),
        );

        $this->rateRepository->save($updatedRate);

        return $updatedRate;
    }
}
