<?php

namespace App\Domain\Subscription;


use App\Domain\Customer\Customer;
use App\Domain\Parking\Parking;


class Subscription
{
    private int $id;

    private Customer $customer;

    private Parking $parking;

    private string $startDate;

    private string $endDate;

    private float $rate;

    private array $weeklySlots;

    public function __construct(
        int $id,
        Customer $customer,
        Parking $parking,
        string $startDate,
        string $endDate,
        float $rate,
        array $weeklySlots
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->parking = $parking;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->rate = $rate;
        $this->weeklySlots = $weeklySlots;
    }
}