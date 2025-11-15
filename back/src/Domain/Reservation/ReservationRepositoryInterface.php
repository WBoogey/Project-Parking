<?php

namespace App\Domain\Reservation;

use App\Domain\Reservation\Reservation;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\Parking\Parking;
use App\Domain\Customer\Customer;
use App\Domain\Rate\Rate;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;

    /**
     * Récupérer une réservation par son ID, intervalle, parking, client ou facture
     */
    public function findById(int $id): ?Reservation;
    public function findByInterval(TimeInterval $interval): array;
    public function findByParking(Parking $parking): array;
    public function findByCustomer(Customer $customer): array;
    public function getRateForReservation(Reservation $reservation): ?Rate;
    public function delete(Reservation $reservation): void;
}