<?php

namespace App\Domain\Owner;

use App\Domain\User\User;
use App\Domain\Parking\Parking;

class Owner extends User
{
    private array $parkings = [];

    public function __construct(
        string $email,
        string $password,
        string $name,
        string $lastName,
        array $parkings = []
    ) {
        parent::__construct($email, $password, $name, $lastName);
        $this->parkings = $parkings;
    }
    public function getParkings(): array
    {
        return $this->parkings;
    }

    public function addParking(Parking $parking): void
    {
        $this->parkings[] = $parking;
    }
}