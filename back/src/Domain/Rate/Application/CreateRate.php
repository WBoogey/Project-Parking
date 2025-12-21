<?php

declare(strict_types=1);

namespace App\Domain\Rate\Application;

use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\Rate\RateType;
use App\Domain\User\UserId;
use InvalidArgumentException;

final class CreateRate
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
        ParkingId $parkingId,
        RateType $type,
        float $price,
        string $calculationRule = 'fixed',
        ?float $hourlyDiscount = null,
        ?string $duration = null,
    ): Rate {
        // Verify parking exists and belongs to owner
        $parking = $this->parkingRepository->findById($parkingId);

        if ($parking === null) {
            throw new InvalidArgumentException('Parking not found');
        }

        if (!$parking->getOwnerId()->equals($ownerId)) {
            throw new InvalidArgumentException('You do not own this parking');
        }

        // Check if a rate of same type already exists for this parking
        $existingRates = $this->rateRepository->findByParkingId($parkingId);
        foreach ($existingRates as $existingRate) {
            if ($existingRate->getType() === $type) {
                throw new InvalidArgumentException(
                    "A rate of type '{$type->value}' already exists for this parking"
                );
            }
        }

        // Validate price
        if ($price <= 0) {
            throw new InvalidArgumentException('Price must be greater than 0');
        }

        $rate = Rate::create(
            parkingId: $parkingId,
            type: $type,
            calculationRule: $calculationRule,
            price: $price,
            hourlyDiscount: $hourlyDiscount,
            duration: $duration,
        );

        $this->rateRepository->save($rate);

        return $rate;
    }
}
