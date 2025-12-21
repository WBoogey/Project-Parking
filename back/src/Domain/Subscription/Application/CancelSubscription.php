<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Application\Exception\SubscriptionNotFoundException;
use App\Domain\User\UserId;

class CancelSubscription
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
  ) {}

  public function execute(SubscriptionId $subscriptionId, UserId $userId): void
  {
    $subscription = $this->subscriptionRepository->findById($subscriptionId);

    if ($subscription === null) {
      throw new SubscriptionNotFoundException("Subscription not found");
    }

    if (!$subscription->getUserId()->equals($userId)) {
      throw new SubscriptionNotFoundException("Subscription not found");
    }

    $this->subscriptionRepository->delete($subscription);
  }
}
