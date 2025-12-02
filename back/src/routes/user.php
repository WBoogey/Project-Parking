<?php

use App\HTTP\UserController;
use App\Services\UserService;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\SigninWithEmailUser;
use App\Domain\User\Application\GetUserProfile;
use App\Domain\User\Application\UpdateUserProfile;
use App\Domain\User\Application\DeleteUser;

$signupUseCase = new SignupWithEmailUser($userRepository, $jwtService);
$signinUseCase = new SigninWithEmailUser($userRepository, $jwtService);
$getUserProfileUseCase = new GetUserProfile($userRepository);
$updateUserProfileUseCase = new UpdateUserProfile($userRepository);
$deleteUserUseCase = new DeleteUser($userRepository);

$userService = new UserService(
  $signupUseCase,
  $signinUseCase,
  $getUserProfileUseCase,
  $updateUserProfileUseCase,
  $deleteUserUseCase,
);
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

// Routes User (protégées)
$router->get("/api/users/me", [$userController, "me"], "api.users.me");
$router->put(
  "/api/users/me",
  [$userController, "updateProfile"],
  "api.users.update",
);
$router->delete(
  "/api/users/me",
  [$userController, "deleteProfile"],
  "api.users.delete",
);

// Dashboards
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
