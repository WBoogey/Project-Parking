<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Application\Exception\SubscriptionNotFoundException;

class GetSubscription
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
  ) {}

  public function execute(SubscriptionId $subscriptionId): Subscription
  {
    $subscription = $this->subscriptionRepository->findById($subscriptionId);

    if ($subscription === null) {
      throw new SubscriptionNotFoundException("Subscription not found");
    }

    return $subscription;
  }
}
