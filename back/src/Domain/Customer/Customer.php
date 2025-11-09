<?php

namespace App\Domain\Customer;

use App\Domain\User\User;
use App\Domain\Reservation\Reservation;
use App\Domain\Subscription\Subscription;
use App\Domain\Stationing\Stationing;

class Customer extends User
{
    private array $reservations;
    private array $subscriptions;
    private array $stationings;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        array $reservations = [],
        array $subscriptions = [],
        array $stationings = []
    ) {
        parent::__construct($email, $password, $firstName, $lastName);
        $this->reservations = $reservations;
        $this->subscriptions = $subscriptions;
        $this->stationings = $stationings;
    }
}
