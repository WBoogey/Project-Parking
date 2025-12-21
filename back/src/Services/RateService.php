<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\Application\CreateRate;
use App\Domain\Rate\Application\DeleteRate;
use App\Domain\Rate\Application\GetParkingRates;
use App\Domain\Rate\Application\UpdateRate;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateType;
use App\Domain\User\UserId;

final class RateService
{
    public function __construct(
        private readonly CreateRate $createRate,
        private readonly UpdateRate $updateRate,
        private readonly DeleteRate $deleteRate,
        private readonly GetParkingRates $getParkingRates,
    ) {}

    /**
     * @return Rate[]
     */
    public function getRatesForParking(UserId $ownerId, ParkingId $parkingId): array
    {
        return $this->getParkingRates->execute($ownerId, $parkingId);
    }

    public function createRate(
        UserId $ownerId,
        ParkingId $parkingId,
        RateType $type,
        float $price,
        string $calculationRule = 'fixed',
        ?float $hourlyDiscount = null,
        ?string $duration = null,
    ): Rate {
        return $this->createRate->execute(
            ownerId: $ownerId,
            parkingId: $parkingId,
            type: $type,
            price: $price,
            calculationRule: $calculationRule,
            hourlyDiscount: $hourlyDiscount,
            duration: $duration,
        );
    }

    public function updateRate(
        UserId $ownerId,
        RateId $rateId,
        ?float $price = null,
        ?string $calculationRule = null,
        ?float $hourlyDiscount = null,
        ?string $duration = null,
    ): Rate {
        return $this->updateRate->execute(
            ownerId: $ownerId,
            rateId: $rateId,
            price: $price,
            calculationRule: $calculationRule,
            hourlyDiscount: $hourlyDiscount,
            duration: $duration,
        );
    }

    public function deleteRate(UserId $ownerId, RateId $rateId): void
    {
        $this->deleteRate->execute($ownerId, $rateId);
    }
}
