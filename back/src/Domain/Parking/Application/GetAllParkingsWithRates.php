<?php

declare(strict_types=1);

namespace App\Domain\Parking\Application;

use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateRepositoryInterface;

final class GetAllParkingsWithRates
{
    public function __construct(
        private readonly ParkingRepositoryInterface $parkingRepository,
        private readonly RateRepositoryInterface $rateRepository,
    ) {}

    /**
     * Get all parkings with their associated rates
     *
     * @return array<array{parking: Parking, rates: Rate[]}>
     */
    public function execute(): array
    {
        $parkings = $this->parkingRepository->findAll();
        $result = [];

        foreach ($parkings as $parking) {
            $rates = $this->rateRepository->findByParkingId($parking->getId());
            $result[] = [
                'parking' => $parking,
                'rates' => $rates,
            ];
        }

        return $result;
    }
}
