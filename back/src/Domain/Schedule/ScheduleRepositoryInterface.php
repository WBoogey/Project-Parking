<?php

namespace App\Domain\Schedule;

use App\Domain\Schedule\Schedule;

interface ScheduleRepositoryInterface
{
    public function save(Schedule $schedule): void;

    /** 
     * Récupérer un planning par son ID, jours ou heures d'ouverture et par parking
     */
    public function findById(int $id): ?Schedule;
    public function findByOpeningDays(string $openingDays): array;
    public function findByOpeningHours(string $openingHours): array;
    public function findByParkingId(int $parkingId): array;
    public function delete(Schedule $schedule): void;
}