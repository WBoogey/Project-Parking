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
        string $firstName,
        string $lastName,
        array $parkings = []
    ) {
        parent::__construct($email, $password, $firstName, $lastName);
        $this->parkings = $parkings;
    }
}