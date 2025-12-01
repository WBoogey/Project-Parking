<?php

namespace App\Infrastructure\Middleware;

use App\Domain\User\JwtServiceInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\UserId;
use ReflectionMethod;

class MiddlewareHandler
{
  public function __construct(
    private readonly JwtServiceInterface $jwtService,
    private readonly UserRepositoryInterface $userRepository,
  ) {}

  public function handle(object $controller, string $method): array
  {
    $reflection = new ReflectionMethod($controller, $method);

    // Récupérer les attributs de la méthode et de la classe
    $methodAttributes = $reflection->getAttributes();
    $classAttributes = $reflection->getDeclaringClass()->getAttributes();

    $attributes = array_merge($classAttributes, $methodAttributes);

    $requiresAuth = false;
    $requiresOwner = false;
    $requiresCustomer = false;

    $requiresAdmin = false;

    foreach ($attributes as $attribute) {
      $name = $attribute->getName();

      if ($name === RequireAuth::class) {
        $requiresAuth = true;
      }
      if ($name === RequireOwner::class) {
        $requiresAuth = true;
        $requiresOwner = true;
      }
      if ($name === RequireCustomer::class) {
        $requiresAuth = true;
        $requiresCustomer = true;
      }
      if ($name === RequireAdmin::class) {
        $requiresAuth = true;
        $requiresAdmin = true;
      }
    }

    if (!$requiresAuth) {
      return ["success" => true];
    }

    $authResult = $this->authenticate();
    if (!$authResult["success"]) {
      return $authResult;
    }

    if ($requiresOwner && !AuthContext::isOwner()) {
      return [
        "success" => false,
        "error" => "Access denied. Owner role required.",
        "status" => 403,
      ];
    }

    if ($requiresCustomer && !AuthContext::isCustomer()) {
      return [
        "success" => false,
        "error" => "Access denied. Customer role required.",
        "status" => 403,
      ];
    }

    if ($requiresAdmin && !AuthContext::isAdmin()) {
      return [
        "success" => false,
        "error" => "Access denied. Admin role required.",
        "status" => 403,
      ];
    }

    return ["success" => true];
  }

  private function authenticate(): array
  {
    $token = $this->extractToken();

    if ($token === null) {
      return [
        "success" => false,
        "error" => "Authentication required.",
        "status" => 401,
      ];
    }

    $payload = $this->jwtService->verifyToken($token);

    if ($payload === null) {
      return [
        "success" => false,
        "error" => "Invalid or expired token.",
        "status" => 401,
      ];
    }

    $userId = UserId::fromString($payload["userId"]);
    $user = $this->userRepository->findById($userId);

    if ($user === null) {
      return [
        "success" => false,
        "error" => "User not found.",
        "status" => 401,
      ];
    }

    AuthContext::setUser($user);

    return ["success" => true];
  }

  private function extractToken(): ?string
  {
    return $_COOKIE["auth_token"] ?? null;
  }
}
