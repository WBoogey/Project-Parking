<?php

namespace App\Services;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\Application\CreateSubscription;
use App\Domain\Subscription\Application\CreateSubscriptionResult;
use App\Domain\Subscription\Application\GetSubscription;
use App\Domain\Subscription\Application\GetUserSubscriptions;
use App\Domain\Subscription\Application\GetParkingSubscriptions;
use App\Domain\Subscription\Application\UpdateSubscription;
use App\Domain\Subscription\Application\CancelSubscription;
use App\Domain\Subscription\Application\Exception\SubscriptionNotFoundException;
use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\User\UserId;

class SubscriptionService
{
  public function __construct(
    private readonly CreateSubscription $createSubscriptionUseCase,
    private readonly GetSubscription $getSubscriptionUseCase,
    private readonly GetUserSubscriptions $getUserSubscriptionsUseCase,
    private readonly GetParkingSubscriptions $getParkingSubscriptionsUseCase,
    private readonly UpdateSubscription $updateSubscriptionUseCase,
    private readonly CancelSubscription $cancelSubscriptionUseCase,
  ) {}

  public function create(
    UserId $userId,
    ParkingId $parkingId,
    string $startDate,
    string $endDate,
    RateId $rateId,
    array $weeklySlots = [],
    ?string $stripeCustomerId = null,
  ): CreateSubscriptionResult {
    return $this->createSubscriptionUseCase->execute(
      userId: $userId,
      parkingId: $parkingId,
      startDate: $startDate,
      endDate: $endDate,
      rateId: $rateId,
      weeklySlots: $weeklySlots,
      stripeCustomerId: $stripeCustomerId,
    );
  }

  /**
   * @throws SubscriptionNotFoundException
   */
  public function getById(SubscriptionId $subscriptionId): Subscription
  {
    return $this->getSubscriptionUseCase->execute($subscriptionId);
  }

  /**
   * @return Subscription[]
   */
  public function getByUser(UserId $userId): array
  {
    return $this->getUserSubscriptionsUseCase->execute($userId);
  }

  /**
   * @return Subscription[]
   */
  public function getByParking(ParkingId $parkingId): array
  {
    return $this->getParkingSubscriptionsUseCase->execute($parkingId);
  }

  /**
   * @throws SubscriptionNotFoundException
   */
  public function update(
    SubscriptionId $subscriptionId,
    ?string $startDate = null,
    ?string $endDate = null,
    ?RateId $rateId = null,
    ?array $weeklySlots = null,
  ): Subscription {
    return $this->updateSubscriptionUseCase->execute(
      subscriptionId: $subscriptionId,
      startDate: $startDate,
      endDate: $endDate,
      rateId: $rateId,
      weeklySlots: $weeklySlots,
    );
  }

  /**
   * @throws SubscriptionNotFoundException
   */
  public function cancel(SubscriptionId $subscriptionId, UserId $userId): void
  {
    $this->cancelSubscriptionUseCase->execute($subscriptionId, $userId);
  }
}
