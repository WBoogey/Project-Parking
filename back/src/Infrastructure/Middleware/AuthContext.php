<?php

namespace App\Infrastructure\Middleware;

use App\Domain\User\User;

class AuthContext
{
  private static ?User $currentUser = null;

  public static function setUser(User $user): void
  {
    self::$currentUser = $user;
  }

  public static function getUser(): ?User
  {
    return self::$currentUser;
  }

  public static function getUserId(): ?string
  {
    return self::$currentUser?->getId()->toString();
  }

  public static function isAuthenticated(): bool
  {
    return self::$currentUser !== null;
  }

  public static function isOwner(): bool
  {
    return self::$currentUser?->isOwner() ?? false;
  }

  public static function isCustomer(): bool
  {
    return self::$currentUser?->isCustomer() ?? false;
  }

  public static function isAdmin(): bool
  {
    return self::$currentUser?->isAdmin() ?? false;
  }

  public static function clear(): void
  {
    self::$currentUser = null;
  }
}
