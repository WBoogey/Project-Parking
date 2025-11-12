<?php

namespace App\Domain\Parking;

use App\Domain\Owner\Owner;

class Parking 
{
    private int $id;

    private string $location;

    private int $capacity;

    private array $reservations = [];

    private Owner $owner;

    public function __construct(int $id, string $location, int $capacity, Owner $owner)
    {
        $this->id = $id;
        $this->location = $location;
        $this->capacity = $capacity;
        $this->owner = $owner;
    }
}