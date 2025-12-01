<?php

use App\HTTP\UserController;
use App\Services\UserService;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\SigninWithEmailUser;
use App\Infrastructure\Repository\UserRepositorySQL;
use App\Infrastructure\adaptaters\FirebaseJwtService;
use App\Infrastructure\Core\Config\Database;
use App\Infrastructure\Core\Config\Config;

// Dépendances
$config = Config::getInstance();
$pdo = Database::getInstance()->getConnection();

$userRepository = new UserRepositorySQL($pdo);
$jwtService = new FirebaseJwtService(
  secret: $config->get("jwt.secret_key"),
  expirationSeconds: $config->get("jwt.expiration"),
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
