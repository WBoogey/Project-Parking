<?php

declare(strict_types=1);

use App\HTTP\SubscriptionController;
use App\Services\SubscriptionService;
use App\Domain\Subscription\Application\CreateSubscription;
use App\Domain\Subscription\Application\GetSubscription;
use App\Domain\Subscription\Application\GetUserSubscriptions;
use App\Domain\Subscription\Application\GetParkingSubscriptions;
use App\Domain\Subscription\Application\UpdateSubscription;
use App\Domain\Subscription\Application\CancelSubscription;
use App\Infrastructure\Repository\RateRepositorySQL;
use App\Infrastructure\adaptaters\StripePaymentAdapter;
use App\Infrastructure\Core\Config\Config;

// Rate repository for price lookup
$rateRepository = new RateRepositorySQL($pdo);

// Payment gateway (Stripe) - optional based on config
$paymentGateway = null;
$stripeSecretKey = $config->get("stripe.secret_key");

if (!empty($stripeSecretKey)) {
  $paymentGateway = new StripePaymentAdapter(
    secretKey: $stripeSecretKey,
    defaultSuccessUrl: $config->get("stripe.success_url") ??
      "http://localhost:3000/payment/success",
    defaultCancelUrl: $config->get("stripe.cancel_url") ??
      "http://localhost:3000/payment/cancel",
  );
}

// Use cases
$createSubscriptionUseCase = new CreateSubscription(
  subscriptionRepository: $subscriptionRepository,
  rateRepository: $rateRepository,
  paymentGateway: $paymentGateway,
  successUrl: $config->get("stripe.success_url") ??
    "http://localhost:3000/payment/success",
  cancelUrl: $config->get("stripe.cancel_url") ??
    "http://localhost:3000/payment/cancel",
);
$getSubscriptionUseCase = new GetSubscription($subscriptionRepository);
$getUserSubscriptionsUseCase = new GetUserSubscriptions(
  $subscriptionRepository,
);
$getParkingSubscriptionsUseCase = new GetParkingSubscriptions(
  $subscriptionRepository,
);
$updateSubscriptionUseCase = new UpdateSubscription($subscriptionRepository);
$cancelSubscriptionUseCase = new CancelSubscription($subscriptionRepository);

// Service
$subscriptionService = new SubscriptionService(
  $createSubscriptionUseCase,
  $getSubscriptionUseCase,
  $getUserSubscriptionsUseCase,
  $getParkingSubscriptionsUseCase,
  $updateSubscriptionUseCase,
  $cancelSubscriptionUseCase,
);

// Controller
$subscriptionController = new SubscriptionController($subscriptionService);

// Routes

// Liste des abonnements de l'utilisateur connecté
$router->get(
  "/api/subscriptions",
  [$subscriptionController, "index"],
  "api.subscriptions.index",
);

// Détail d'un abonnement
$router->get(
  "/api/subscriptions/:id",
  [$subscriptionController, "show"],
  "api.subscriptions.show",
);

// Créer un abonnement (avec paiement Stripe si configuré)
$router->post(
  "/api/subscriptions",
  [$subscriptionController, "create"],
  "api.subscriptions.create",
);

// Modifier un abonnement
$router->put(
  "/api/subscriptions/:id",
  [$subscriptionController, "update"],
  "api.subscriptions.update",
);

// Annuler un abonnement
$router->delete(
  "/api/subscriptions/:id",
  [$subscriptionController, "cancel"],
  "api.subscriptions.cancel",
);
