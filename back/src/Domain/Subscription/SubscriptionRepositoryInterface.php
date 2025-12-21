<?php

declare(strict_types=1);

namespace App\Domain\Subscription;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\User\UserId;

interface SubscriptionRepositoryInterface
{
  public function save(Subscription $subscription): void;

  /**
   * Save subscription with Stripe payment information
   */
  public function saveWithPayment(
    Subscription $subscription,
    string $stripeSessionId,
    string $stripePaymentStatus,
    int $amount,
    string $currency = "eur",
  ): void;

  /**
   * Update payment status after Stripe webhook
   */
  public function updatePaymentStatus(
    SubscriptionId $subscriptionId,
    string $stripePaymentStatus,
    ?\DateTimeImmutable $paidAt = null,
  ): void;

  /**
   * Find subscription by Stripe session ID
   */
  public function findByStripeSessionId(string $stripeSessionId): ?Subscription;

  public function findById(SubscriptionId $id): ?Subscription;

  /**
   * @return Subscription[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Subscription[]
   */
  public function findByUserId(UserId $userId): array;

  /**
   * @return Subscription[]
   */
  public function findByRateId(RateId $rateId): array;

  public function delete(Subscription $subscription): void;
}
