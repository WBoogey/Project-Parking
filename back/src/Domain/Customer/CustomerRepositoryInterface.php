<?php



namespace App\Domain\Customer;

use App\Domain\User\UserId;
use App\Domain\Reservation\Reservation;
use App\Domain\Subscription\Subscription;
use App\Domain\Stationing\Stationing;

interface CustomerRepositoryInterface
{
  /** @return Reservation[] */
  public function getReservations(UserId $customerId): array;

  /** @return Subscription[] */
  public function getSubscriptions(UserId $customerId): array;

  /** @return Stationing[] */
  public function getStationings(UserId $customerId): array;
}
