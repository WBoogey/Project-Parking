<?php

namespace App\Domain\TimeInterval;

class TimeInterval
{
    private string $dayOfWeek;

    private string $startHour;

    private string $endHour;

    public function __construct(string $dayOfWeek, string $startHour, string $endHour)
    {
        $this->dayOfWeek = $dayOfWeek;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
    }
}