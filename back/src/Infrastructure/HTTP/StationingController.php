<?php

declare(strict_types=1);

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireAuth;
use App\Services\StationingService;
use App\Domain\Parking\ParkingId;
use App\Domain\Payment\PaymentException;

class StationingController extends Controllers
{
  public function __construct(
    private readonly StationingService $stationingService,
  ) {}

  /**
   * GET /api/stationings - List user's stationings history
   */
  #[RequireAuth]
  public function index(): bool|string
  {
    $user = AuthContext::getUser();
    $stationings = $this->stationingService->getUserStationings($user->getId());

    $data = array_map(
      fn($stationing) => [
        "id" => $stationing->getId()->toString(),
        "userId" => $stationing->getUserId()->toString(),
        "parkingId" => $stationing->getParkingId()->toString(),
        "startTime" => $stationing->getStartTime()->format('Y-m-d H:i:s'),
        "endTime" => $stationing->getEndTime()?->format('Y-m-d H:i:s'),
        "status" => $stationing->getStatus()->value,
        "rateId" => $stationing->getRateId()?->toString(),
        "amount" => $stationing->getAmount(),
        "isFree" => $stationing->isFree(),
      ],
      $stationings,
    );

    return $this->success(data: $data, message: "User stationings");
  }

  /**
   * POST /api/stationings/enter - Enter a parking
   */
  #[RequireAuth]
  public function enter(): bool|string
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

    if (empty($parkingId)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Missing required field: parkingId",
        "status" => 422,
      ]);
    }

    try {
      $stationing = $this->stationingService->enter(
        userId: $user->getId(),
        parkingId: ParkingId::fromString($parkingId),
      );

      return $this->json(201, [
        "status" => "success",
        "message" => "You have entered the parking",
        "data" => [
          "id" => $stationing->getId()->toString(),
          "userId" => $stationing->getUserId()->toString(),
          "parkingId" => $stationing->getParkingId()->toString(),
          "startTime" => $stationing->getStartTime()->format('Y-m-d H:i:s'),
          "status" => $stationing->getStatus()->value,
        ],
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    } catch (\RuntimeException $e) {
      return $this->json(409, [
        "type" => "https://httpstatuses.com/409",
        "title" => "Conflict",
        "detail" => $e->getMessage(),
        "status" => 409,
      ]);
    }
  }

  /**
   * POST /api/stationings/exit - Exit a parking
   * Returns checkout URL if payment is required
   */
  #[RequireAuth]
  public function exit(): bool|string
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

    if (empty($parkingId)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Missing required field: parkingId",
        "status" => 422,
      ]);
    }

    try {
      $result = $this->stationingService->exit(
        userId: $user->getId(),
        parkingId: ParkingId::fromString($parkingId),
      );

      $responseData = [
        "id" => $result->stationing->getId()->toString(),
        "userId" => $result->stationing->getUserId()->toString(),
        "parkingId" => $result->stationing->getParkingId()->toString(),
        "startTime" => $result->stationing->getStartTime()->format('Y-m-d H:i:s'),
        "endTime" => $result->stationing->getEndTime()?->format('Y-m-d H:i:s'),
        "status" => $result->stationing->getStatus()->value,
        "isFree" => $result->isFree,
        "amount" => $result->amount,
      ];

      if ($result->checkoutUrl !== null) {
        $responseData["checkoutUrl"] = $result->checkoutUrl;
        $responseData["paymentStatus"] = "pending";
      }

      $message = $result->isFree 
        ? "You have exited the parking (free - subscription active)" 
        : "You have exited the parking. Please complete payment.";

      return $this->success(
        data: $responseData,
        message: $message,
      );
    } catch (\RuntimeException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    } catch (PaymentException $e) {
      return $this->json(502, [
        "type" => "https://httpstatuses.com/502",
        "title" => "Payment Gateway Error",
        "detail" => "Failed to create payment session: " . $e->getMessage(),
        "status" => 502,
      ]);
    }
  }

  /**
   * GET /api/stationings/active/:parkingId - Get active stationing for user in a parking
   */
  #[RequireAuth]
  public function active(string $parkingId): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $stationing = $this->stationingService->getActiveStationing(
        userId: $user->getId(),
        parkingId: ParkingId::fromString($parkingId),
      );

      if ($stationing === null) {
        return $this->json(404, [
          "type" => "https://httpstatuses.com/404",
          "title" => "Not Found",
          "detail" => "No active stationing found in this parking",
          "status" => 404,
        ]);
      }

      return $this->success(
        data: [
          "id" => $stationing->getId()->toString(),
          "userId" => $stationing->getUserId()->toString(),
          "parkingId" => $stationing->getParkingId()->toString(),
          "startTime" => $stationing->getStartTime()->format('Y-m-d H:i:s'),
          "status" => $stationing->getStatus()->value,
        ],
        message: "Active stationing found",
      );
    } catch (\InvalidArgumentException $e) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid parking ID format",
        "status" => 422,
      ]);
    }
  }
}
