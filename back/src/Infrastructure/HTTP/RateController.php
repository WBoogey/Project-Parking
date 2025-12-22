<?php

declare(strict_types=1);

namespace App\Infrastructure\HTTP;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateType;
use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireOwner;
use App\Services\RateService;
use InvalidArgumentException;

class RateController extends Controllers
{
    public function __construct(private readonly RateService $rateService) {}

    #[RequireOwner]
    public function index(string $parkingId): bool|string
    {
        $user = AuthContext::getUser();

        try {
            $parkingIdObj = ParkingId::fromString($parkingId);
            $rates = $this->rateService->getRatesForParking($user->getId(), $parkingIdObj);

            $data = array_map(
                fn(Rate $rate) => $this->formatRate($rate),
                $rates
            );

            return $this->success(data: $data, message: 'Rates retrieved successfully');
        } catch (InvalidArgumentException $e) {
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => $e->getMessage(),
                'status' => 422,
            ]);
        }
    }

    #[RequireOwner]
    public function store(string $parkingId): bool|string
    {
        $user = AuthContext::getUser();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return $this->json(400, [
                'type' => 'https://httpstatuses.com/400',
                'title' => 'Bad Request',
                'detail' => 'Invalid JSON body',
                'status' => 400,
            ]);
        }

        // Validate required fields
        $type = $input['type'] ?? null;
        $price = $input['price'] ?? null;

        if (empty($type) || $price === null) {
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => 'Missing required fields: type, price',
                'status' => 422,
            ]);
        }

        // Validate type enum
        $rateType = RateType::tryFrom($type);
        if ($rateType === null) {
            $validTypes = array_map(fn(RateType $t) => $t->value, RateType::cases());
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => 'Invalid rate type. Valid types: ' . implode(', ', $validTypes),
                'status' => 422,
            ]);
        }

        try {
            $parkingIdObj = ParkingId::fromString($parkingId);

            $rate = $this->rateService->createRate(
                ownerId: $user->getId(),
                parkingId: $parkingIdObj,
                type: $rateType,
                price: (float) $price,
                calculationRule: $input['calculationRule'] ?? 'fixed',
                hourlyDiscount: isset($input['hourlyDiscount']) ? (float) $input['hourlyDiscount'] : null,
                duration: $input['duration'] ?? null,
            );

            return $this->json(201, [
                'status' => 'success',
                'message' => 'Rate created successfully',
                'data' => $this->formatRate($rate),
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => $e->getMessage(),
                'status' => 422,
            ]);
        }
    }

    #[RequireOwner]
    public function update(string $parkingId, string $rateId): bool|string
    {
        $user = AuthContext::getUser();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return $this->json(400, [
                'type' => 'https://httpstatuses.com/400',
                'title' => 'Bad Request',
                'detail' => 'Invalid JSON body',
                'status' => 400,
            ]);
        }

        try {
            $rateIdObj = RateId::fromString($rateId);

            $rate = $this->rateService->updateRate(
                ownerId: $user->getId(),
                rateId: $rateIdObj,
                price: isset($input['price']) ? (float) $input['price'] : null,
                calculationRule: $input['calculationRule'] ?? null,
                hourlyDiscount: isset($input['hourlyDiscount']) ? (float) $input['hourlyDiscount'] : null,
                duration: $input['duration'] ?? null,
            );

            return $this->success(
                data: $this->formatRate($rate),
                message: 'Rate updated successfully'
            );
        } catch (InvalidArgumentException $e) {
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => $e->getMessage(),
                'status' => 422,
            ]);
        }
    }

    #[RequireOwner]
    public function destroy(string $parkingId, string $rateId): bool|string
    {
        $user = AuthContext::getUser();

        try {
            $rateIdObj = RateId::fromString($rateId);

            $this->rateService->deleteRate($user->getId(), $rateIdObj);

            return $this->success(data: [], message: 'Rate deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->json(422, [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Unprocessable Entity',
                'detail' => $e->getMessage(),
                'status' => 422,
            ]);
        }
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
