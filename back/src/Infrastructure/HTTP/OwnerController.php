<?php

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireOwner;
use App\Services\OwnerService;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;

class OwnerController extends Controllers
{
    public function __construct(private readonly OwnerService $ownerService) {}

    #[RequireOwner]
    public function getParkings(): bool|string
    {
        $user = AuthContext::getUser();
        $parkings = $this->ownerService->getParkings($user->getId());

        $data = array_map(
            fn(Parking $parking) => [
                "id" => $parking->getId()->toString(),
                "location" => $parking->getLocation(),
                "capacity" => $parking->getCapacity(),
                "ownerId" => $parking->getOwnerId()->toString(),
            ],
            $parkings
        );

        return $this->success(data: $data, message: "Owner parkings retrieved");
    }

    #[RequireOwner]
    public function addParking(): bool|string
    {
        $user = AuthContext::getUser();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->json(400, [
                "type" => "https://httpstatuses.com/400",
                "title" => "Bad Request",
                "detail" => "Invalid JSON body",
                "status" => 400,
            ]);
        }

        $location = $this->sanitize($input["location"] ?? "");
        $capacity = (int) ($input["capacity"] ?? 0);

        if (empty($location) || $capacity <= 0) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Missing or invalid fields: location, capacity",
                "status" => 422,
            ]);
        }

        $parking = Parking::create(
            location: $location,
            capacity: $capacity,
            ownerId: $user->getId(),
        );

        $this->ownerService->addParking($user->getId(), $parking);

        return $this->json(201, [
            "status" => "success",
            "message" => "Parking added successfully",
            "data" => [
                "id" => $parking->getId()->toString(),
                "location" => $parking->getLocation(),
                "capacity" => $parking->getCapacity(),
                "ownerId" => $parking->getOwnerId()->toString(),
            ],
        ]);
    }

    #[RequireOwner]
    public function removeParking(): bool|string
    {
        $user = AuthContext::getUser();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->json(400, [
                "type" => "https://httpstatuses.com/400",
                "title" => "Bad Request",
                "detail" => "Invalid JSON body",
                "status" => 400,
            ]);
        }

        $parkingId = $input["parkingId"] ?? "";

        if (empty($parkingId)) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Missing field: parkingId",
                "status" => 422,
            ]);
        }

        try {
            $this->ownerService->removeParking(
                $user->getId(),
                ParkingId::fromString($parkingId)
            );

            return $this->success(data: [], message: "Parking removed successfully");
        } catch (\InvalidArgumentException $e) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Invalid parking ID format",
                "status" => 422,
            ]);
        }
    }

    #[RequireOwner]
    public function updateParking(string $id): bool|string
    {
        $user = AuthContext::getUser();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->json(400, [
                "type" => "https://httpstatuses.com/400",
                "title" => "Bad Request",
                "detail" => "Invalid JSON body",
                "status" => 400,
            ]);
        }

        $location = isset($input["location"]) ? $this->sanitize($input["location"]) : null;
        $capacity = isset($input["capacity"]) ? (int) $input["capacity"] : null;

        if ($location === null && $capacity === null) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "At least one field must be provided: location, capacity",
                "status" => 422,
            ]);
        }

        if ($capacity !== null && $capacity <= 0) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Capacity must be greater than 0",
                "status" => 422,
            ]);
        }

        try {
            $parking = $this->ownerService->updateParking(
                parkingId: $id,
                ownerId: $user->getId()->toString(),
                location: $location,
                capacity: $capacity,
            );

            return $this->success(
                data: [
                    "id" => $parking->getId()->toString(),
                    "location" => $parking->getLocation(),
                    "capacity" => $parking->getCapacity(),
                    "ownerId" => $parking->getOwnerId()->toString(),
                ],
                message: "Parking updated successfully"
            );
        } catch (\InvalidArgumentException $e) {
            $status = str_contains($e->getMessage(), 'not found') ? 404 : 403;
            return $this->json($status, [
                "type" => "https://httpstatuses.com/{$status}",
                "title" => $status === 404 ? "Not Found" : "Forbidden",
                "detail" => $e->getMessage(),
                "status" => $status,
            ]);
        }
    }
}
