<?php

declare(strict_types=1);

namespace App\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\Subscription\SubscriptionId;
use App\Infrastructure\Core\Config\Config;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controllers
{
  public function __construct(
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
    private readonly string $webhookSecret,
  ) {}

  /**
   * Handle Stripe webhook events
   * POST /api/stripe/webhook
   */
  public function handle(): bool|string
  {
    $payload = file_get_contents("php://input");
    $sigHeader = $_SERVER["HTTP_STRIPE_SIGNATURE"] ?? "";

    if (empty($sigHeader)) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Missing Stripe signature header",
        "status" => 400,
      ]);
    }

    try {
      $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
    } catch (SignatureVerificationException $e) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid signature: " . $e->getMessage(),
        "status" => 400,
      ]);
    } catch (\UnexpectedValueException $e) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid payload: " . $e->getMessage(),
        "status" => 400,
      ]);
    }

    // Handle the event
    switch ($event->type) {
      case "checkout.session.completed":
        return $this->handleCheckoutSessionCompleted($event->data->object);

      case "checkout.session.expired":
        return $this->handleCheckoutSessionExpired($event->data->object);

      case "payment_intent.payment_failed":
        return $this->handlePaymentFailed($event->data->object);

      default:
        // Acknowledge receipt of unknown event types
        return $this->json(200, [
          "status" => "success",
          "message" => "Webhook received: " . $event->type,
        ]);
    }
  }

  /**
   * Handle successful checkout session
   */
  private function handleCheckoutSessionCompleted(object $session): bool|string
  {
    $sessionId = $session->id;

    $subscription = $this->subscriptionRepository->findByStripeSessionId($sessionId);

    if ($subscription === null) {
      // Log this but don't fail - might be for a different entity type
      error_log("Stripe webhook: No subscription found for session {$sessionId}");
      return $this->json(200, [
        "status" => "success",
        "message" => "Webhook received, no matching subscription",
      ]);
    }

    // Update payment status to success
    $this->subscriptionRepository->updatePaymentStatus(
      subscriptionId: $subscription->getId(),
      stripePaymentStatus: "success",
      paidAt: new \DateTimeImmutable(),
    );

    return $this->json(200, [
      "status" => "success",
      "message" => "Payment confirmed for subscription " . $subscription->getId()->toString(),
    ]);
  }

  /**
   * Handle expired checkout session
   */
  private function handleCheckoutSessionExpired(object $session): bool|string
  {
    $sessionId = $session->id;

    $subscription = $this->subscriptionRepository->findByStripeSessionId($sessionId);

    if ($subscription === null) {
      return $this->json(200, [
        "status" => "success",
        "message" => "Webhook received, no matching subscription",
      ]);
    }

    // Update payment status to cancelled/expired
    $this->subscriptionRepository->updatePaymentStatus(
      subscriptionId: $subscription->getId(),
      stripePaymentStatus: "cancelled",
    );

    return $this->json(200, [
      "status" => "success",
      "message" => "Session expired for subscription " . $subscription->getId()->toString(),
    ]);
  }

  /**
   * Handle failed payment
   */
  private function handlePaymentFailed(object $paymentIntent): bool|string
  {
    // Payment intents don't directly link to our session IDs
    // We need to check metadata if we stored subscription_id there
    $subscriptionId = $paymentIntent->metadata->subscription_id ?? null;

    if ($subscriptionId === null) {
      return $this->json(200, [
        "status" => "success",
        "message" => "Webhook received, no subscription_id in metadata",
      ]);
    }

    try {
      $this->subscriptionRepository->updatePaymentStatus(
        subscriptionId: SubscriptionId::fromString($subscriptionId),
        stripePaymentStatus: "failed",
      );

      return $this->json(200, [
        "status" => "success",
        "message" => "Payment failed for subscription " . $subscriptionId,
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(200, [
        "status" => "success",
        "message" => "Invalid subscription ID in metadata",
      ]);
    }
  }
}
