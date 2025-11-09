<?php

namespace App\Domain\Reservation;

use App\Domain\Reservation\Reservation;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;

    /**
     * Récupérer une réservation par son ID, intervalle, parking ou client
     */
    public function findById(int $id): ?Reservation;
    public function findByInterval(TimeInterval $interval): array;
    public function findByParking(Parking $parking): array;
    public function findByCustomer(Customer $customer): array;
    public function delete(Reservation $reservation): void;
}