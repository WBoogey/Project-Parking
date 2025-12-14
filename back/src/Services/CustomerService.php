<?php

namespace App\Services;

use App\Domain\Customer\Application\GetCustomerReservations;
use App\Domain\Customer\Application\GetCustomerSubscriptions;
use App\Domain\Customer\Application\GetCustomerStationings;
use App\Domain\User\UserId;
use App\Domain\Reservation\Reservation;
use App\Domain\Subscription\Subscription;
use App\Domain\Stationing\Stationing;

class CustomerService
{
    public function __construct(
        private readonly GetCustomerReservations $getReservationsUseCase,
        private readonly GetCustomerSubscriptions $getSubscriptionsUseCase,
        private readonly GetCustomerStationings $getStationingsUseCase,
    ) {}

    /**
     * @return Reservation[]
     */
    public function getReservations(UserId $customerId): array
    {
        return $this->getReservationsUseCase->execute($customerId);
    }

    /**
     * @return Subscription[]
     */
    public function getSubscriptions(UserId $customerId): array
    {
        return $this->getSubscriptionsUseCase->execute($customerId);
    }

    /**
     * @return Stationing[]
     */
    public function getStationings(UserId $customerId): array
    {
        return $this->getStationingsUseCase->execute($customerId);
    }
}
