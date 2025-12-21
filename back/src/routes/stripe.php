<?php

declare(strict_types=1);

use App\HTTP\StripeWebhookController;
use App\Infrastructure\Core\Config\Config;

// Stripe webhook secret
$stripeWebhookSecret = $config->get("stripe.webhook_secret") ?? "";

// Controller
$stripeWebhookController = new StripeWebhookController(
  subscriptionRepository: $subscriptionRepository,
  webhookSecret: $stripeWebhookSecret,
);

// Webhook endpoint - no auth required (Stripe will verify via signature)
$router->post(
  "/api/stripe/webhook",
  [$stripeWebhookController, "handle"],
  "api.stripe.webhook",
);
