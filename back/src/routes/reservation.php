<?php

declare(strict_types=1);

use App\Infrastructure\HTTP\ReservationController;
use App\Services\ReservationService;
use App\Domain\Reservation\Application\CreateReservation;
use App\Domain\Reservation\Application\CancelReservation;
use App\Domain\Invoice\Application\GenerateReservationInvoice;
use App\Infrastructure\Repository\ReservationRepositorySQL;
use App\Infrastructure\Repository\ParkingRepositorySQL;
use App\Infrastructure\Repository\SubscriptionRepositorySQL;
use App\Infrastructure\Repository\RateRepositorySQL;
use App\Infrastructure\Repository\InvoiceRepositorySQL;
use App\Infrastructure\adaptaters\StripePaymentAdapter;

// Repositories
$reservationRepository = new ReservationRepositorySQL($pdo);
$parkingRepository = new ParkingRepositorySQL($pdo);
$subscriptionRepository = new SubscriptionRepositorySQL($pdo);
$rateRepository = new RateRepositorySQL($pdo);
$invoiceRepository = new InvoiceRepositorySQL($pdo);

// Payment gateway (Stripe)
$paymentGateway = null;
$stripeSecretKey = $config->get("stripe.secret_key");

if (!empty($stripeSecretKey)) {
  $paymentGateway = new StripePaymentAdapter(
    secretKey: $stripeSecretKey,
    defaultSuccessUrl: $config->get("stripe.success_url") ??
      "http://localhost:3000/reservation/success",
    defaultCancelUrl: $config->get("stripe.cancel_url") ??
      "http://localhost:3000/reservation/cancel",
  );
}

// Use cases
$createReservationUseCase = new CreateReservation(
  reservationRepository: $reservationRepository,
  parkingRepository: $parkingRepository,
  subscriptionRepository: $subscriptionRepository,
  rateRepository: $rateRepository,
  paymentGateway: $paymentGateway,
  successUrl: $config->get("stripe.success_url") ?? "http://localhost:3000/reservation/success",
  cancelUrl: $config->get("stripe.cancel_url") ?? "http://localhost:3000/reservation/cancel",
);

$cancelReservationUseCase = new CancelReservation(
  reservationRepository: $reservationRepository,
  paymentGateway: $paymentGateway,
);

$generateInvoiceUseCase = new GenerateReservationInvoice(
  invoiceRepository: $invoiceRepository,
  reservationRepository: $reservationRepository,
  parkingRepository: $parkingRepository,
);

// Service
$reservationService = new ReservationService(
  $createReservationUseCase,
  $cancelReservationUseCase,
  $generateInvoiceUseCase,
  $reservationRepository,
);

// Controller
$reservationController = new ReservationController($reservationService);

// Routes

// List user's reservations
$router->get(
  "/api/reservations",
  [$reservationController, "index"],
  "api.reservations.index",
);

// Get reservation details
$router->get(
  "/api/reservations/:id",
  [$reservationController, "show"],
  "api.reservations.show",
);

// Create a new reservation
$router->post(
  "/api/reservations",
  [$reservationController, "create"],
  "api.reservations.create",
);

// Cancel a reservation
$router->delete(
  "/api/reservations/:id",
  [$reservationController, "cancel"],
  "api.reservations.cancel",
);

// Generate invoice for a reservation
$router->post(
  "/api/reservations/:id/invoice",
  [$reservationController, "invoice"],
  "api.reservations.invoice",
);
