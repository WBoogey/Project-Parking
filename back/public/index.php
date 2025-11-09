<?php

use Core\Router;

require_once __DIR__ . "/../vendor/autoload.php";

// CORS basique
function cors()
{
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
  header("Content-Type: application/json; charset=UTF-8");
  header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With",
  );
  header("Access-Control-Max-Age: 3600"); // Cache

  if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
  }
}
cors();

set_exception_handler(function ($e) {
  http_response_code(500);
  echo json_encode([
    "status" => "error",
    "message" => "Internal Server Error",
    "details" => $e->getMessage(), // A Enlever en prod
  ]);
});

// Initialisation du router
$url = $_SERVER["REQUEST_URI"];
$router = new Router($url);

// Chargement de tous les fichiers de routes
require_once __DIR__ . "/../routes/user.php";
require_once __DIR__ . "/../routes/app.php";

// Exécution du router
$router->run();
var_dump($router->run());
