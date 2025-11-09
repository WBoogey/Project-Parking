<?php

namespace App\Domain\Schedule;

use App\Domain\Schedule\Schedule;

interface ScheduleRepositoryInterface
{
    public function save(Schedule $schedule): void;

    /** 
     * Récupérer un planning par son ID, jours d'ouverture ou heures d'ouverture
     */
    public function findById(int $id): ?Schedule;
    public function findByOpeningDays(string $openingDays): array;
    public function findByOpeningHours(string $openingHours): array;
    public function delete(Schedule $schedule): void;
}