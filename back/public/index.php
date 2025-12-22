<?php

declare(strict_types=1);

function cors(): void
{
  $allowedOrigins = [
    "http://localhost:5173",
    "http://localhost:3000",
    "http://127.0.0.1:5173",
  ];

  $origin = $_SERVER["HTTP_ORIGIN"] ?? "";

  if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
    header("Access-Control-Allow-Credentials: true");
  }

  header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
  header("Content-Type: application/json; charset=UTF-8");
  header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Stripe-Signature",
  );
  header("Access-Control-Max-Age: 3600");

  if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
  }
}
cors();

require_once __DIR__ . "/../vendor/autoload.php";

use App\Infrastructure\Core\Config\Router;
use App\Infrastructure\Core\Config\Config;
use App\Infrastructure\Core\Config\Database;
use App\Infrastructure\Middleware\MiddlewareHandler;
use App\Infrastructure\Repository\UserRepositorySQL;
use App\Infrastructure\Repository\OwnerRepositorySQL;
use App\Infrastructure\Repository\CustomerRepositorySQL;
use App\Infrastructure\Repository\SubscriptionRepositorySQL;
use App\Infrastructure\Repository\RateRepositorySQL;
use App\Infrastructure\Repository\ParkingRepositorySQL;
use App\Infrastructure\adaptaters\FirebaseJwtService;

set_exception_handler(function ($e) {
  http_response_code(500);
  echo json_encode([
    "type" => "https://httpstatuses.com/500",
    "title" => "Internal Server Error",
    "detail" => $e->getMessage(),
    "status" => 500,
  ]);
});

$config = Config::getInstance();
$pdo = Database::getInstance()->getConnection();

$userRepository = new UserRepositorySQL($pdo);
$ownerRepository = new OwnerRepositorySQL($pdo);
$customerRepository = new CustomerRepositorySQL($pdo);
$subscriptionRepository = new SubscriptionRepositorySQL($pdo);
$rateRepository = new RateRepositorySQL($pdo);
$parkingRepository = new ParkingRepositorySQL($pdo);

$jwtService = new FirebaseJwtService(
  secret: $config->get("jwt.secret_key"),
  expirationSeconds: $config->get("jwt.expiration"),
);

$middlewareHandler = new MiddlewareHandler($jwtService, $userRepository);

$url = $_SERVER["REQUEST_URI"];

if (($pos = strpos($url, "?")) !== false) {
  $url = substr($url, 0, $pos);
}

$router = new Router($url);
$router->setMiddlewareHandler($middlewareHandler);

try {
  require_once __DIR__ . "/../src/routes/user.php";
  require_once __DIR__ . "/../src/routes/owner.php";
  require_once __DIR__ . "/../src/routes/customer.php";
  require_once __DIR__ . "/../src/routes/subscription.php";
  require_once __DIR__ . "/../src/routes/rate.php";
  require_once __DIR__ . "/../src/routes/parking.php";
  require_once __DIR__ . "/../src/routes/stripe.php";
  require_once __DIR__ . "/../src/routes/stationing.php";
  require_once __DIR__ . "/../src/routes/reservation.php";
  require_once __DIR__ . "/../src/routes/app.php";
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "type" => "https://httpstatuses.com/500",
    "title" => "Internal Server Error",
    "detail" => "Failed to load routes: " . $e->getMessage(),
    "file" => $e->getFile() . ":" . $e->getLine(),
    "status" => 500,
  ]);
  exit();
}

try {
  echo $router->run();
} catch (Throwable $e) {
  $message = $e->getMessage();

  if (
    strpos($message, "No matching routes") !== false ||
    strpos($message, "REQUEST_METHOD") !== false
  ) {
    http_response_code(404);
    echo json_encode([
      "type" => "https://httpstatuses.com/404",
      "title" => "Not Found",
      "detail" => "Route not found",
      "status" => 404,
    ]);
  } else {
    error_log(
      "Router error: " .
        $message .
        " in " .
        $e->getFile() .
        ":" .
        $e->getLine(),
    );
    http_response_code(500);
    echo json_encode([
      "type" => "https://httpstatuses.com/500",
      "title" => "Internal Server Error",
      "detail" => $message,
      "file" => $e->getFile() . ":" . $e->getLine(),
      "status" => 500,
    ]);
  }
}
