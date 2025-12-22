<?php

declare(strict_types=1);

namespace App\Domain\Parking\Application;

use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateRepositoryInterface;

class GetParkingById
{
    public function __construct(
        private readonly ParkingRepositoryInterface $parkingRepository,
        private readonly RateRepositoryInterface $rateRepository,
    ) {}

    /**
     * @return array{parking: Parking, rates: array}|null
     */
    public function execute(string $parkingId): ?array
    {
        $parking = $this->parkingRepository->findById(ParkingId::fromString($parkingId));

        if ($parking === null) {
            return null;
        }

        $rates = $this->rateRepository->findByParkingId(ParkingId::fromString($parkingId));

        return [
            'parking' => $parking,
            'rates' => $rates,
        ];
    }
}
