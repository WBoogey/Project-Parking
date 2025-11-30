<?php

use App\Infrastructure\Core\Config\Router;
use Dotenv\Dotenv;

require_once __DIR__ . "/../vendor/autoload.php";

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

// CORS basique
function cors()
{
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
  header("Content-Type: application/json; charset=UTF-8");
  header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With",
  );
  header("Access-Control-Max-Age: 3600");

  if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
  }
}
cors();

set_exception_handler(function ($e) {
  http_response_code(500);
  echo json_encode([
    "type" => "https://httpstatuses.com/500",
    "title" => "Internal Server Error",
    "detail" => $e->getMessage(),
    "status" => 500,
  ]);
});

// Initialisation du router
$url = $_SERVER["REQUEST_URI"];
$router = new Router($url);

// Chargement des routes
require_once __DIR__ . "/../src/routes/user.php";
require_once __DIR__ . "/../src/routes/app.php";

// Exécution du router
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
