<?php

declare(strict_types=1);

namespace App\HTTP;

use App\Domain\Parking\Application\GetAllParkingsWithRates;
use App\Domain\Parking\Parking;
use App\Domain\Rate\Rate;
use App\Infrastructure\Core\Config\Controllers;

class ParkingController extends Controllers
{
    public function __construct(
        private readonly GetAllParkingsWithRates $getAllParkingsWithRates,
    ) {}

    /**
     * List all parkings with their rates (public endpoint)
     */
    public function index(): bool|string
    {
        $parkingsWithRates = $this->getAllParkingsWithRates->execute();

        $data = array_map(
            fn(array $item) => [
                'parking' => $this->formatParking($item['parking']),
                'rates' => array_map(
                    fn(Rate $rate) => $this->formatRate($rate),
                    $item['rates']
                ),
            ],
            $parkingsWithRates
        );

        return $this->success(data: $data, message: 'Parkings retrieved successfully');
    }

    private function formatParking(Parking $parking): array
    {
        return [
            'id' => $parking->getId()->toString(),
            'location' => $parking->getLocation(),
            'capacity' => $parking->getCapacity(),
            'ownerId' => $parking->getOwnerId()->toString(),
        ];
    }

    private function formatRate(Rate $rate): array
    {
        return [
            'id' => $rate->getId()->toString(),
            'parkingId' => $rate->getParkingId()->toString(),
            'type' => $rate->getType()->value,
            'calculationRule' => $rate->getCalculationRule(),
            'price' => $rate->getPrice(),
            'hourlyDiscount' => $rate->getHourlyDiscount(),
            'duration' => $rate->getDuration(),
        ];
    }
}
