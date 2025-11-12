<?php

namespace App\Domain\Schedule;



class Schedule
{
    private int $id;

    private string $openingDays;

    private string $openingHours;


    public function __construct(int $id, string $openingDays, string $openingHours)
    {
        $this->id = $id;
        $this->openingDays = $openingDays;
        $this->openingHours = $openingHours;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOpeningDays(): string
    {
        return $this->openingDays;
    }

    public function getOpeningHours(): string
    {
        return $this->openingHours;
    }
}