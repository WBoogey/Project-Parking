<?php

namespace App\Domain\Stationing;

use App\Domain\Customer\Customer;
use App\Domain\Parking\Parking;
use App\Domain\Stationing\StationingStatus;


class Stationing
{
    private int $id;

    private \DateTime $startTime;

    private \DateTime $endTime;

    private StationingStatus $status;

    private Customer $customer;

    private Parking $parking;

    public function __construct(
        int $id,
        \DateTime $startTime,
        \DateTime $endTime,
        StationingStatus $status,
        Customer $customer,
        Parking $parking
    ) {
        $this->id = $id;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->status = $status;
        $this->customer = $customer;
        $this->parking = $parking;
    }
}