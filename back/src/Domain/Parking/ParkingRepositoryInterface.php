<?php

namespace App\Domain\Parking;

use App\Domain\Owner\Owner;
use App\Domain\Parking\Parking;

interface ParkingRepositoryInterface
{
    public function save(Parking $parking): void;

    /**
     * Récupérer un parking par son ID, location et owner
     * 
     */
    public function findById(int $id): ?Parking;
    public function findByLocation(string $location): ?Parking;

    public function findByOwner(Owner $owner): ?array;
    public function delete(Parking $parking): void;
}