<?php

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireAuth;
use App\Infrastructure\Middleware\RequireOwner;
use App\Infrastructure\Middleware\RequireCustomer;
use App\Services\UserService;
use App\Domain\User\Application\Exception\UserAlreadyExistsException;
use App\Domain\User\Application\Exception\InvalidCredentialsException;
use App\Domain\User\Application\Exception\UserNotFoundException;
use App\Domain\User\UserRole;

class UserController extends Controllers
{
  private const COOKIE_NAME = "auth_token";
  private const COOKIE_EXPIRATION = 3600; // 1 hour

  public function __construct(private readonly UserService $userService) {}

  private function setAuthCookie(string $token): void
  {
    $secure = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on";
    setcookie(self::COOKIE_NAME, $token, [
      "expires" => time() + self::COOKIE_EXPIRATION,
      "path" => "/",
      "httponly" => true,
      "secure" => $secure,
      "samesite" => "Strict",
    ]);
  }

  private function clearAuthCookie(): void
  {
    setcookie(self::COOKIE_NAME, "", [
      "expires" => time() - 3600,
      "path" => "/",
      "httponly" => true,
      "samesite" => "Strict",
    ]);
  }

  public function signup(): bool|string
  {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid JSON body",
        "status" => 400,
      ]);
    }

    $email = $this->sanitize($input["email"] ?? "");
    $password = $input["password"] ?? "";
    $firstName = $this->sanitize($input["firstName"] ?? "");
    $lastName = $this->sanitize($input["lastName"] ?? "");
    $role = $input["role"] ?? "customer";

    if (
      empty($email) ||
      empty($password) ||
      empty($firstName) ||
      empty($lastName)
    ) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" =>
          "Missing required fields: email, password, firstName, lastName",
        "status" => 422,
      ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid email format",
        "status" => 422,
      ]);
    }

    $userRole = UserRole::tryFrom($role) ?? UserRole::CUSTOMER;

    try {
      $token = $this->userService->signup(
        email: $email,
        password: $password,
        firstName: $firstName,
        lastName: $lastName,
        role: $userRole,
      );

      $this->setAuthCookie($token);

      return $this->success(data: [], message: "User registered successfully");
    } catch (UserAlreadyExistsException $e) {
      return $this->json(409, [
        "type" => "https://httpstatuses.com/409",
        "title" => "Conflict",
        "detail" => $e->getMessage(),
        "status" => 409,
      ]);
    }
  }

  public function signin(): bool|string
  {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid JSON body",
        "status" => 400,
      ]);
    }

    $email = $this->sanitize($input["email"] ?? "");
    $password = $input["password"] ?? "";

    if (empty($email) || empty($password)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Missing required fields: email, password",
        "status" => 422,
      ]);
    }

    try {
      $token = $this->userService->signin(email: $email, password: $password);

      $this->setAuthCookie($token);

      return $this->success(data: [], message: "Login successful");
    } catch (InvalidCredentialsException $e) {
      return $this->json(401, [
        "type" => "https://httpstatuses.com/401",
        "title" => "Unauthorized",
        "detail" => "Invalid email or password",
        "status" => 401,
      ]);
    }
  }

  public function signout(): bool|string
  {
    $this->clearAuthCookie();
    AuthContext::clear();

    return $this->success(data: [], message: "Logged out successfully");
  }

  #[RequireAuth]
  public function me(): bool|string
  {
    $user = AuthContext::getUser();

    return $this->success(
      data: [
        "id" => $user->getId()->toString(),
        "email" => $user->getEmail(),
        "firstName" => $user->getFirstName(),
        "lastName" => $user->getLastName(),
        "role" => $user->getRole()->value,
      ],
      message: "User profile",
    );
  }

  #[RequireAuth]
  public function updateProfile(): bool|string
  {
    $user = AuthContext::getUser();
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
      return $this->json(400, [
        "type" => "https://httpstatuses.com/400",
        "title" => "Bad Request",
        "detail" => "Invalid JSON body",
        "status" => 400,
      ]);
    }

    $email = isset($input["email"]) ? $this->sanitize($input["email"]) : null;
    $firstName = isset($input["firstName"])
      ? $this->sanitize($input["firstName"])
      : null;
    $lastName = isset($input["lastName"])
      ? $this->sanitize($input["lastName"])
      : null;
    $password = $input["password"] ?? null;

    if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return $this->json(422, [
        "type" => "https://httpstatuses.com/422",
        "title" => "Unprocessable Entity",
        "detail" => "Invalid email format",
        "status" => 422,
      ]);
    }

    try {
      $updatedUser = $this->userService->updateProfile(
        userId: $user->getId(),
        email: $email,
        firstName: $firstName,
        lastName: $lastName,
        password: $password,
      );

      return $this->success(
        data: [
          "id" => $updatedUser->getId()->toString(),
          "email" => $updatedUser->getEmail(),
          "firstName" => $updatedUser->getFirstName(),
          "lastName" => $updatedUser->getLastName(),
          "role" => $updatedUser->getRole()->value,
        ],
        message: "Profile updated successfully",
      );
    } catch (UserAlreadyExistsException $e) {
      return $this->json(409, [
        "type" => "https://httpstatuses.com/409",
        "title" => "Conflict",
        "detail" => $e->getMessage(),
        "status" => 409,
      ]);
    } catch (UserNotFoundException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    }
  }

  #[RequireAuth]
  public function deleteProfile(): bool|string
  {
    $user = AuthContext::getUser();

    try {
      $this->userService->deleteUser($user->getId());
      $this->clearAuthCookie();
      AuthContext::clear();

      return $this->success(data: [], message: "Account deleted successfully");
    } catch (UserNotFoundException $e) {
      return $this->json(404, [
        "type" => "https://httpstatuses.com/404",
        "title" => "Not Found",
        "detail" => $e->getMessage(),
        "status" => 404,
      ]);
    }
  }

  #[RequireOwner]
  public function ownerDashboard(): bool|string
  {
    $user = AuthContext::getUser();

    return $this->success(
      data: [
        "message" => "Welcome to owner dashboard",
        "userId" => $user->getId()->toString(),
      ],
      message: "Owner dashboard",
    );
  }

  #[RequireCustomer]
  public function customerDashboard(): bool|string
  {
    $user = AuthContext::getUser();

    return $this->success(
      data: [
        "message" => "Welcome to customer dashboard",
        "userId" => $user->getId()->toString(),
      ],
      message: "Customer dashboard",
    );
  }
}
