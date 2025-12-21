<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\User\UserId;

class GetUserSubscriptions
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
  ) {}

  /**
   * @return Subscription[]
   */
  public function execute(UserId $userId): array
  {
    return $this->subscriptionRepository->findByUserId($userId);
  }
}
