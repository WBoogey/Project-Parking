<?php

use App\HTTP\UserController;
use App\Services\UserService;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\SigninWithEmailUser;

// Dépendances (réutilisées depuis index.php via $userRepository et $jwtService)
$signupUseCase = new SignupWithEmailUser($userRepository, $jwtService);
$signinUseCase = new SigninWithEmailUser($userRepository, $jwtService);

$userService = new UserService($signupUseCase, $signinUseCase);
$userController = new UserController($userService);

// Routes Auth (publiques)
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
$router->post(
  "/api/auth/signout",
  [$userController, "signout"],
  "api.auth.signout",
);

// Routes protégées
$router->get("/api/users/me", [$userController, "me"], "api.users.me");
$router->get(
  "/api/owner/dashboard",
  [$userController, "ownerDashboard"],
  "api.owner.dashboard",
);
$router->get(
  "/api/customer/dashboard",
  [$userController, "customerDashboard"],
  "api.customer.dashboard",
);
