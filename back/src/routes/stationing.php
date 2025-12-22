<?php

declare(strict_types=1);

use App\Infrastructure\HTTP\StationingController;
use App\Services\StationingService;
use App\Domain\Stationing\Application\EnterParking;
use App\Domain\Stationing\Application\ExitParking;
use App\Infrastructure\Repository\StationingRepositorySQL;
use App\Infrastructure\Repository\ParkingRepositorySQL;
use App\Infrastructure\Repository\SubscriptionRepositorySQL;
use App\Infrastructure\Repository\RateRepositorySQL;
use App\Infrastructure\adaptaters\StripePaymentAdapter;

// Repositories
$stationingRepository = new StationingRepositorySQL($pdo);
$parkingRepository = new ParkingRepositorySQL($pdo);
$subscriptionRepository = new SubscriptionRepositorySQL($pdo);
$rateRepository = new RateRepositorySQL($pdo);

// Payment gateway (Stripe)
$paymentGateway = null;
$stripeSecretKey = $config->get("stripe.secret_key");

if (!empty($stripeSecretKey)) {
  $paymentGateway = new StripePaymentAdapter(
    secretKey: $stripeSecretKey,
    defaultSuccessUrl: $config->get("stripe.success_url") ??
      "http://localhost:5173/stationing/success",
    defaultCancelUrl: $config->get("stripe.cancel_url") ??
      "http://localhost:5173/stationing/cancel",
  );
}

// Use cases
$enterParkingUseCase = new EnterParking(
  stationingRepository: $stationingRepository,
  parkingRepository: $parkingRepository,
);

$exitParkingUseCase = new ExitParking(
  stationingRepository: $stationingRepository,
  parkingRepository: $parkingRepository,
  subscriptionRepository: $subscriptionRepository,
  rateRepository: $rateRepository,
  paymentGateway: $paymentGateway,
);

// Service
$stationingService = new StationingService(
  $enterParkingUseCase,
  $exitParkingUseCase,
  $stationingRepository,
);

// Controller
$stationingController = new StationingController($stationingService);

// Routes

// List user's stationings history
$router->get(
  "/api/stationings",
  [$stationingController, "index"],
  "api.stationings.index",
);

// Enter a parking
$router->post(
  "/api/stationings/enter",
  [$stationingController, "enter"],
  "api.stationings.enter",
);

// Exit a parking
$router->post(
  "/api/stationings/exit",
  [$stationingController, "exit"],
  "api.stationings.exit",
);

// Get active stationing in a parking
$router->get(
  "/api/stationings/active/:parkingId",
  [$stationingController, "active"],
  "api.stationings.active",
);
