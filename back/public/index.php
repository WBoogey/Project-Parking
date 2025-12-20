<?php

function cors()
{
  $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
  $allowed_origins = ['http://localhost:5173', 'http://127.0.0.1:5173'];

  if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
  } else {
    header("Access-Control-Allow-Origin: http://localhost:5173");
  }

  header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
  header("Content-Type: application/json; charset=UTF-8");
  header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With",
  );
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Max-Age: 3600");

  if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
  }
}
cors();

use App\Infrastructure\Core\Config\Router;
use App\Infrastructure\Core\Config\Config;
use App\Infrastructure\Core\Config\Database;
use App\Infrastructure\Middleware\MiddlewareHandler;
use App\Infrastructure\Repository\UserRepositorySQL;
use App\Infrastructure\Repository\OwnerRepositorySQL;
use App\Infrastructure\Repository\CustomerRepositorySQL;
use App\Infrastructure\adaptaters\FirebaseJwtService;

require_once __DIR__ . "/../vendor/autoload.php";

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

$jwtService = new FirebaseJwtService(
  secret: $config->get("jwt.secret_key"),
  expirationSeconds: $config->get("jwt.expiration"),
);

$middlewareHandler = new MiddlewareHandler($jwtService, $userRepository);

$url = $_SERVER["REQUEST_URI"];
$router = new Router($url);
$router->setMiddlewareHandler($middlewareHandler);

require_once __DIR__ . "/../src/routes/user.php";
require_once __DIR__ . "/../src/routes/owner.php";
require_once __DIR__ . "/../src/routes/customer.php";
require_once __DIR__ . "/../src/routes/app.php";

try {
  echo $router->run();
} catch (Exception $e) {
  http_response_code(404);
  echo json_encode([
    "type" => "https://httpstatuses.com/404",
    "title" => "Not Found",
    "detail" => "Route not found",
    "status" => 404,
  ]);
}
