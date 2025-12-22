<?php

declare(strict_types=1);

namespace App\Infrastructure\HTTP;

use App\Domain\Parking\Application\GetAllParkingsWithRates;
use App\Domain\Parking\Application\GetParkingById;
use App\Domain\Parking\Parking;
use App\Domain\Rate\Rate;
use App\Infrastructure\Core\Config\Controllers;

class ParkingController extends Controllers
{
    public function __construct(
        private readonly GetAllParkingsWithRates $getAllParkingsWithRates,
        private readonly GetParkingById $getParkingById,
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

    /**
     * Get a single parking by ID with its rates (public endpoint)
     */
    public function show(string $id): bool|string
    {
        $result = $this->getParkingById->execute($id);

        if ($result === null) {
            return $this->json(404, [
                'type' => 'https://httpstatuses.com/404',
                'title' => 'Not Found',
                'detail' => 'Parking not found',
                'status' => 404,
            ]);
        }

        $data = [
            'parking' => $this->formatParking($result['parking']),
            'rates' => array_map(
                fn(Rate $rate) => $this->formatRate($rate),
                $result['rates']
            ),
        ];

        return $this->success(data: $data, message: 'Parking retrieved successfully');
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
