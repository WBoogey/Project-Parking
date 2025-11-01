<?php

namespace App\Domain\Reservation;

use App\Domain\TimeInterval\TimeInterval;
use App\Domain\Parking\Parking;
use App\Domain\User\User;
use App\Domain\Customer\Customer;

class Reservation
{
    private int $id;

    private TimeInterval $interval;

    private Parking $parking;

    private Customer $customer;

    public function __construct(int $id, TimeInterval $interval, Parking $parking, Customer $customer)
    {
        $this->id = $id;
        $this->interval = $interval;
        $this->parking = $parking;
        $this->customer = $customer;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInterval(): TimeInterval
    {
        return $this->interval;
    }

    public function getParking(): Parking
    {
        return $this->parking;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}