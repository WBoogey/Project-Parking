<?php

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRole;

/**
 * @param array<string, mixed> $data
 */
function hydrateUser(array $data): User
{
  return User::create(
    email: $data["email"],
    password: $data["password"],
    firstName: $data["first_name"],
    lastName: $data["last_name"],
    role: UserRole::from($data["role"]),
    id: UserId::fromString($data["id"]),
  );
}
