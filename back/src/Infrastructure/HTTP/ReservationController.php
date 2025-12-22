<?php

declare(strict_types=1);

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireAuth;
use App\Services\ReservationService;
use App\Domain\Reservation\ReservationId;
use App\Domain\Parking\ParkingId;
use App\Domain\Payment\PaymentException;
use DateTime;

class ReservationController extends Controllers
{
  public function __construct(
    private readonly ReservationService $reservationService,
  ) {}

  /**
   * GET /api/reservations - List user's reservations
   */
  #[RequireAuth]
  public function index(): bool|string
  {
    $user = AuthContext::getUser();
    $reservations = $this->reservationService->getUserReservations($user->getId());

    $data = array_map(
      fn($reservation) => [
        "id" => $reservation->getId()->toString(),
        "userId" => $reservation->getUserId()->toString(),
        "parkingId" => $reservation->getParkingId()->toString(),
        "startTime" => $reservation->getStartTime()->format('Y-m-d H:i:s'),
        "endTime" => $reservation->getEndTime()->format('Y-m-d H:i:s'),
        "status" => $reservation->getStatus()->value,
        "rateId" => $reservation->getRateId()?->toString(),
        "amount" => $reservation->getAmount(),
        "isFree" => $reservation->isFree(),
      ],
      $reservations,
    );

    return $this->success(data: $data, message: "User reservations");
  }

  /**
   * GET /api/reservations/:id - Get reservation details
   */
  #[RequireAuth]
  public function show(string $id): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $reservation = $this->reservationService->getById(
        ReservationId::fromString($id),
      );

      if ($reservation === null) {
        return $this->json(404, [
          "type" => "https://httpstatuses.com/404",
          "title" => "Not Found",
          "detail" => "Reservation not found",
          "status" => 404,
        ]);
      }

      if (!$reservation->getUserId()->equals($user->getId())) {
        return $this->json(403, [
          "type" => "https://httpstatuses.com/403",
          "title" => "Forbidden",
          "detail" => "You do not have access to this reservation",
          "status" => 403,
        ]);
      }

      return $this->success(
        data: [
          "id" => $reservation->getId()->toString(),
          "userId" => $reservation->getUserId()->toString(),
          "parkingId" => $reservation->getParkingId()->toString(),
          "startTime" => $reservation->getStartTime()->format('Y-m-d H:i:s'),
          "endTime" => $reservation->getEndTime()->format('Y-m-d H:i:s'),
          "status" => $reservation->getStatus()->value,
          "rateId" => $reservation->getRateId()?->toString(),
          "amount" => $reservation->getAmount(),
          "isFree" => $reservation->isFree(),
        ],
        message: "Reservation details",
      );
    } catch (\InvalidArgumentException) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid reservation ID format",
        "status" => 422,
      ]);
    }
  }

  /**
   * POST /api/reservations - Create a new reservation
   */
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
    $startTime = $input["startTime"] ?? "";
    $endTime = $input["endTime"] ?? "";

    if (empty($parkingId) || empty($startTime) || empty($endTime)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Missing required fields: parkingId, startTime, endTime",
        "status" => 422,
      ]);
    }

    try {
      $result = $this->reservationService->create(
        userId: $user->getId(),
        parkingId: ParkingId::fromString($parkingId),
        startTime: new DateTime($startTime),
        endTime: new DateTime($endTime),
      );

      $reservation = $result->reservation;

      $responseData = [
        "id" => $reservation->getId()->toString(),
        "userId" => $reservation->getUserId()->toString(),
        "parkingId" => $reservation->getParkingId()->toString(),
        "startTime" => $reservation->getStartTime()->format('Y-m-d H:i:s'),
        "endTime" => $reservation->getEndTime()->format('Y-m-d H:i:s'),
        "status" => $reservation->getStatus()->value,
        "isFree" => $result->isFree,
        "amount" => $reservation->getAmount(),
      ];

      if ($result->checkoutUrl !== null) {
        $responseData["checkoutUrl"] = $result->checkoutUrl;
        $responseData["stripeSessionId"] = $result->stripeSessionId;
        $responseData["paymentStatus"] = "pending";
      }

      $message = $result->isFree
        ? "Reservation created (free - subscription active)"
        : ($result->checkoutUrl !== null
            ? "Reservation created. Please complete payment."
            : "Reservation created");

      return $this->json(201, [
        "status" => "success",
        "message" => $message,
        "data" => $responseData,
      ]);
    } catch (\InvalidArgumentException $e) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => $e->getMessage(),
        "status" => 422,
      ]);
    } catch (\RuntimeException $e) {
      return $this->json(409, [
        "type" => "https://httpstatuses.com/409",
        "title" => "Conflict",
        "detail" => $e->getMessage(),
        "status" => 409,
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
   * DELETE /api/reservations/:id - Cancel a reservation
   */
  #[RequireAuth]
  public function cancel(string $id): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $result = $this->reservationService->cancel(
        ReservationId::fromString($id),
        $user->getId(),
      );

      $responseData = [
        "id" => $result->reservation->getId()->toString(),
        "status" => $result->reservation->getStatus()->value,
        "wasRefunded" => $result->wasRefunded,
      ];

      if ($result->refundId !== null) {
        $responseData["refundId"] = $result->refundId;
      }

      if ($result->errorMessage !== null) {
        $responseData["refundError"] = $result->errorMessage;
      }

      $message = $result->wasRefunded
        ? "Reservation cancelled and refunded"
        : "Reservation cancelled";

      return $this->success(
        data: $responseData,
        message: $message,
      );
    } catch (\InvalidArgumentException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    } catch (\RuntimeException $e) {
      return $this->json(403, [
        "type" => "https://httpstatuses.com/403",
        "title" => "Forbidden",
        "detail" => $e->getMessage(),
        "status" => 403,
      ]);
    }
  }

  /**
   * POST /api/reservations/:id/invoice - Generate invoice for a reservation
   */
  #[RequireAuth]
  public function invoice(string $id): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $invoice = $this->reservationService->generateInvoice(
        ReservationId::fromString($id),
        $user->getId(),
      );

      return $this->json(201, [
        "status" => "success",
        "message" => "Invoice generated",
        "data" => [
          "id" => $invoice->getId()->toString(),
          "invoiceNumber" => $invoice->getInvoiceNumber(),
          "type" => $invoice->getType()->value,
          "amount" => $invoice->getAmount(),
          "formattedAmount" => $invoice->getFormattedAmount(),
          "currency" => $invoice->getCurrency(),
          "status" => $invoice->getStatus()->value,
          "description" => $invoice->getDescription(),
          "issuedAt" => $invoice->getIssuedAt()->format('Y-m-d H:i:s'),
          "paidAt" => $invoice->getPaidAt()?->format('Y-m-d H:i:s'),
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
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => $e->getMessage(),
        "status" => 400,
      ]);
    }
  }
}
