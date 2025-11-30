<?php

use App\HTTP\UserController;
use App\Services\UserService;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\SigninWithEmailUser;
use App\Infrastructure\Repository\UserRepositorySQL;
use App\Infrastructure\adaptaters\FirebaseJwtService;
use App\Infrastructure\Core\Config\Database;

// Configuration DB
$dbConfig = [
  "host" => $_ENV["DB_HOST"] ?? "localhost",
  "port" => (int) ($_ENV["DB_PORT"] ?? 3306),
  "database" => $_ENV["DB_NAME"] ?? "parking",
  "username" => $_ENV["DB_USER"] ?? "root",
  "password" => $_ENV["DB_PASSWORD"] ?? "",
  "charset" => "utf8mb4",
];

// Dépendances
$db = Database::getInstance($dbConfig);
$pdo = $db->getConnection();

$userRepository = new UserRepositorySQL($pdo);
$jwtService = new FirebaseJwtService(
  secret: $_ENV["JWT_SECRET"] ?? "your-secret-key",
  expirationSeconds: (int) ($_ENV["JWT_EXPIRATION"] ?? 3600),
);

$signupUseCase = new SignupWithEmailUser($userRepository, $jwtService);
$signinUseCase = new SigninWithEmailUser($userRepository, $jwtService);

$userService = new UserService($signupUseCase, $signinUseCase);
$userController = new UserController($userService);

// Routes Auth
$router->post(
  "/api/auth/signup",
  [$userController, "signup"],
  "api.auth.signup",
);
$router->post(
  "/api/auth/signin",
  [$userController, "signin"],
  "api.auth.signin",
);
