<?php

namespace App\Domain\User\Application;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\Application\Exception\UserNotFoundException;
use App\Domain\User\Application\Exception\UserAlreadyExistsException;

class UpdateUserProfile
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository,
  ) {}

  public function execute(
    UserId $userId,
    ?string $email = null,
    ?string $firstName = null,
    ?string $lastName = null,
    ?string $password = null,
  ): User {
    $user = $this->userRepository->findById($userId);

    if ($user === null) {
      throw new UserNotFoundException("User not found");
    }

    if ($email !== null && $email !== $user->getEmail()) {
      if ($this->userRepository->emailExists($email)) {
        throw new UserAlreadyExistsException("Email already in use");
      }
    }

    $updatedUser = User::create(
      email: $email ?? $user->getEmail(),
      password: $password !== null
        ? password_hash($password, PASSWORD_BCRYPT)
        : $user->getPassword(),
      firstName: $firstName ?? $user->getFirstName(),
      lastName: $lastName ?? $user->getLastName(),
      role: $user->getRole(),
      id: $userId,
    );

    $this->userRepository->save($updatedUser);

    return $updatedUser;
  }
}
