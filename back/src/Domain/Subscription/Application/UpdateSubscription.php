<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Application\Exception\SubscriptionNotFoundException;
use App\Domain\Rate\RateId;

class UpdateSubscription
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
  ) {}

  public function execute(
    SubscriptionId $subscriptionId,
    ?string $startDate = null,
    ?string $endDate = null,
    ?RateId $rateId = null,
    ?array $weeklySlots = null,
  ): Subscription {
    $subscription = $this->subscriptionRepository->findById($subscriptionId);

    if ($subscription === null) {
      throw new SubscriptionNotFoundException("Subscription not found");
    }

    $updatedSubscription = Subscription::create(
      userId: $subscription->getUserId(),
      parkingId: $subscription->getParkingId(),
      startDate: $startDate ?? $subscription->getStartDate(),
      endDate: $endDate ?? $subscription->getEndDate(),
      rateId: $rateId ?? $subscription->getRateId(),
      weeklySlots: $weeklySlots ?? $subscription->getWeeklySlots(),
      id: $subscriptionId,
    );

    $this->subscriptionRepository->save($updatedSubscription);

    return $updatedSubscription;
  }
}
