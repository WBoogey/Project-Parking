<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Parking\ParkingId;

class GetParkingSubscriptions
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
  ) {}

  /**
   * @return Subscription[]
   */
  public function execute(ParkingId $parkingId): array
  {
    return $this->subscriptionRepository->findByParkingId($parkingId);
  }
}
