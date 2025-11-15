<?php

namespace App\Domain\Stationing;

use App\Domain\Stationing\Stationing;
use App\Domain\Parking\Parking;
use App\Domain\Customer\Customer;

interface StationingRepositoryInterface
{
    public function save(Stationing $stationing): void;

    /**
     * Récupérer un stationnement par son ID, intervalle, parking ou client
     */
    public function findById(int $id): ?Stationing;
    public function findByInterval(\DateTime $startTime, \DateTime $endTime): array;
    public function findByParking(Parking $parking): array;
    public function findByCustomer(Customer $customer): array;
    public function delete(Stationing $stationing): void;
}