<?php

namespace App\Domain\Schedule;

use App\Domain\Parking\ParkingId;

interface ScheduleRepositoryInterface
{
  public function save(Schedule $schedule): void;

  public function findById(ScheduleId $id): ?Schedule;

  /**
   * @return Schedule[]
   */
  public function findByOpeningDays(string $openingDays): array;

  /**
   * @return Schedule[]
   */
  public function findByOpeningHours(string $openingHours): array;

  /**
   * @return Schedule[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  public function delete(Schedule $schedule): void;
}
