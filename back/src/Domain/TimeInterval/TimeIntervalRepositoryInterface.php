<?php

namespace App\Domain\TimeInterval;

use App\Domain\TimeInterval\TimeInterval;

interface TimeIntervalRepositoryInterface
{
    public function save(TimeInterval $timeInterval): void;

    public function findByDayOfWeek(string $dayOfWeek): array;

    public function delete(TimeInterval $timeInterval): void;
}