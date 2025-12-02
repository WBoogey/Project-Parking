<?php

namespace App\Domain\User;

interface UserRepositoryInterface
{
  public function save(User $user): void;

  public function findById(UserId $id): ?User;

  public function findByEmail(string $email): ?User;

  public function findByFullName(string $firstName, string $lastName): ?User;

  /** @return User[] */
  public function findByRole(UserRole $role): array;

  public function emailExists(string $email): bool;

  public function delete(User $user): void;
}
