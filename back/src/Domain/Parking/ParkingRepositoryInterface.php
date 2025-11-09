<?php

namespace App\Domain\Parking;

use App\Domain\Parking\Parking;

interface ParkingRepositoryInterface
{
    public function save(Parking $parking): void;

    /**
     * Récupérer un parking par son ID sa location
     */
    public function findById(int $id): ?Parking;
    public function findByLocation(string $location): ?Parking;

    public function delete(Parking $parking): void;
}