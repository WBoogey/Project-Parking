<?php

declare(strict_types=1);

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireAuth;
use App\Services\SubscriptionService;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\Application\Exception\SubscriptionNotFoundException;
use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\Payment\PaymentException;

class SubscriptionController extends Controllers
{
  public function __construct(
    private readonly SubscriptionService $subscriptionService,
  ) {}

  #[RequireAuth]
  public function index(): bool|string
  {
    $user = AuthContext::getUser();
    $subscriptions = $this->subscriptionService->getByUser($user->getId());

    $data = array_map(
      fn($subscription) => [
        "id" => $subscription->getId()->toString(),
        "userId" => $subscription->getUserId()->toString(),
        "parkingId" => $subscription->getParkingId()->toString(),
        "rateId" => $subscription->getRateId()->toString(),
        "startDate" => $subscription->getStartDate(),
        "endDate" => $subscription->getEndDate(),
        "weeklySlots" => $subscription->getWeeklySlots(),
      ],
      $subscriptions,
    );

    return $this->success(data: $data, message: "User subscriptions");
  }

  #[RequireAuth]
  public function show(string $id): bool|string
  {
    try {
      $subscription = $this->subscriptionService->getById(
        SubscriptionId::fromString($id),
      );
      $user = AuthContext::getUser();

      if (!$subscription->getUserId()->equals($user->getId())) {
        return $this->json(403, [
          "type" => "https://httpstatuses.com/403",
          "title" => "Forbidden",
          "detail" => "You do not have access to this subscription",
          "status" => 403,
        ]);
      }

      return $this->success(
        data: [
          "id" => $subscription->getId()->toString(),
          "userId" => $subscription->getUserId()->toString(),
          "parkingId" => $subscription->getParkingId()->toString(),
          "rateId" => $subscription->getRateId()->toString(),
          "startDate" => $subscription->getStartDate(),
          "endDate" => $subscription->getEndDate(),
          "weeklySlots" => $subscription->getWeeklySlots(),
        ],
        message: "Subscription details",
      );
    } catch (SubscriptionNotFoundException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    }
  }

  #[RequireAuth]
  public function create(): bool|string
  {
    $user = AuthContext::getUser();
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid JSON body",
        "status" => 400,
      ]);
    }

    $parkingId = $input["parkingId"] ?? "";
    $rateId = $input["rateId"] ?? "";
    $startDate = $input["startDate"] ?? "";
    $endDate = $input["endDate"] ?? "";
    $weeklySlots = $input["weeklySlots"] ?? [];
    $stripeCustomerId = $input["stripeCustomerId"] ?? null;

    if (
      empty($parkingId) ||
      empty($rateId) ||
      empty($startDate) ||
      empty($endDate)
    ) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" =>
          "Missing required fields: parkingId, rateId, startDate, endDate",
        "status" => 422,
      ]);
    }

    try {
      $result = $this->subscriptionService->create(
        userId: $user->getId(),
        parkingId: ParkingId::fromString($parkingId),
        startDate: $startDate,
        endDate: $endDate,
        rateId: RateId::fromString($rateId),
        weeklySlots: $weeklySlots,
        stripeCustomerId: $stripeCustomerId,
      );

      $subscription = $result->subscription;

      $responseData = [
        "id" => $subscription->getId()->toString(),
        "userId" => $subscription->getUserId()->toString(),
        "parkingId" => $subscription->getParkingId()->toString(),
        "rateId" => $subscription->getRateId()->toString(),
        "startDate" => $subscription->getStartDate(),
        "endDate" => $subscription->getEndDate(),
        "weeklySlots" => $subscription->getWeeklySlots(),
      ];

      // Include Stripe checkout URL if available
      if ($result->checkoutUrl !== null) {
        $responseData["checkoutUrl"] = $result->checkoutUrl;
        $responseData["stripeSessionId"] = $result->stripeSessionId;
        $responseData["paymentStatus"] = "pending";
      }

      return $this->json(201, [
        "status" => "success",
        "message" =>
          $result->checkoutUrl !== null
            ? "Subscription created. Please complete payment."
            : "Subscription created successfully",
        "data" => $responseData,
      ]);
    } catch (PaymentException $e) {
      return $this->json(502, [
        "type" => "https://httpstatuses.com/502",
        "title" => "Payment Gateway Error",
        "detail" => "Failed to create payment session: " . $e->getMessage(),
        "status" => 502,
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => $e->getMessage(),
        "status" => 422,
      ]);
    }
  }

  #[RequireAuth]
  public function update(string $id): bool|string
  {
    $user = AuthContext::getUser();
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid JSON body",
        "status" => 400,
      ]);
    }

    try {
      $subscriptionId = SubscriptionId::fromString($id);
      $subscription = $this->subscriptionService->getById($subscriptionId);

      if (!$subscription->getUserId()->equals($user->getId())) {
        return $this->json(403, [
          "type" => "https://httpstatuses.com/403",
          "title" => "Forbidden",
          "detail" => "You do not have access to this subscription",
          "status" => 403,
        ]);
      }

      $rateId = isset($input["rateId"])
        ? RateId::fromString($input["rateId"])
        : null;

      $updatedSubscription = $this->subscriptionService->update(
        subscriptionId: $subscriptionId,
        startDate: $input["startDate"] ?? null,
        endDate: $input["endDate"] ?? null,
        rateId: $rateId,
        weeklySlots: $input["weeklySlots"] ?? null,
      );

      return $this->success(
        data: [
          "id" => $updatedSubscription->getId()->toString(),
          "userId" => $updatedSubscription->getUserId()->toString(),
          "parkingId" => $updatedSubscription->getParkingId()->toString(),
          "rateId" => $updatedSubscription->getRateId()->toString(),
          "startDate" => $updatedSubscription->getStartDate(),
          "endDate" => $updatedSubscription->getEndDate(),
          "weeklySlots" => $updatedSubscription->getWeeklySlots(),
        ],
        message: "Subscription updated successfully",
      );
    } catch (SubscriptionNotFoundException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid ID format",
        "status" => 422,
      ]);
    }
  }

  #[RequireAuth]
  public function cancel(string $id): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $this->subscriptionService->cancel(
        SubscriptionId::fromString($id),
        $user->getId(),
      );

      return $this->success(
        data: [],
        message: "Subscription cancelled successfully",
      );
    } catch (SubscriptionNotFoundException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid subscription ID format",
        "status" => 422,
      ]);
    }
  }
}
