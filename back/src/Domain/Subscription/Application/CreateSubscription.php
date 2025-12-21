<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Application;

use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\User\UserId;
use App\Domain\Port\PaymentGatewayInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Payment\PaymentException;

class CreateSubscriptionResult
{
  public function __construct(
    public readonly Subscription $subscription,
    public readonly ?string $checkoutUrl = null,
    public readonly ?string $stripeSessionId = null,
  ) {}
}

class CreateSubscription
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
    private readonly RateRepositoryInterface $rateRepository,
    private readonly ?PaymentGatewayInterface $paymentGateway = null,
    private readonly string $successUrl = "http://localhost:3000/payment/success",
    private readonly string $cancelUrl = "http://localhost:3000/payment/cancel",
  ) {}

  /**
   * @throws PaymentException
   */
  public function execute(
    UserId $userId,
    ParkingId $parkingId,
    string $startDate,
    string $endDate,
    RateId $rateId,
    array $weeklySlots = [],
    ?string $stripeCustomerId = null,
  ): CreateSubscriptionResult {
    // Fetch rate to get price
    $rate = $this->rateRepository->findById($rateId);
    if ($rate === null) {
      throw new \InvalidArgumentException(
        "Rate not found: " . $rateId->toString(),
      );
    }

    // Create subscription entity
    $subscription = Subscription::create(
      userId: $userId,
      parkingId: $parkingId,
      startDate: $startDate,
      endDate: $endDate,
      rateId: $rateId,
      weeklySlots: $weeklySlots,
    );

    // If payment gateway is configured, create Stripe checkout session
    $checkoutUrl = null;
    $stripeSessionId = null;

    if ($this->paymentGateway !== null) {
      // Calculate amount in cents
      $amount = (int) ($rate->getPrice() * 100);

      $paymentRequest = new PaymentRequest(
        amount: $amount,
        currency: "eur",
        description: "Subscription parking - {$startDate} to {$endDate}",
        customerId: $stripeCustomerId,
        metadata: [
          "subscription_id" => $subscription->getId()->toString(),
          "user_id" => $userId->toString(),
          "parking_id" => $parkingId->toString(),
          "rate_id" => $rateId->toString(),
        ],
        successUrl: $this->successUrl . "?session_id={CHECKOUT_SESSION_ID}",
        cancelUrl: $this->cancelUrl .
          "?subscription_id=" .
          $subscription->getId()->toString(),
      );

      $paymentResult = $this->paymentGateway->createPayment($paymentRequest);

      $checkoutUrl = $paymentResult->checkoutUrl;
      $stripeSessionId = $paymentResult->paymentId;

      // Save subscription with stripe session info
      $this->subscriptionRepository->saveWithPayment(
        subscription: $subscription,
        stripeSessionId: $stripeSessionId,
        stripePaymentStatus: "pending",
        amount: $amount,
        currency: "eur",
      );
    } else {
      // No payment gateway, save subscription directly
      $this->subscriptionRepository->save($subscription);
    }

    return new CreateSubscriptionResult(
      subscription: $subscription,
      checkoutUrl: $checkoutUrl,
      stripeSessionId: $stripeSessionId,
    );
  }
}
