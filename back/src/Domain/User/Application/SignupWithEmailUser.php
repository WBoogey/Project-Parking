<?php

namespace App\Domain\User\Application;

use App\Domain\User\Application\Exception\UserAlreadyExistsException;
use App\Domain\Port\JwtServiceInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\UserRole;

class SignupWithEmailUser
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository,
    private readonly JwtServiceInterface $jwtService,
  ) {}

  /**
   * @throws UserAlreadyExistsException
   */
  public function execute(
    string $email,
    string $password,
    string $firstName,
    string $lastName,
    UserRole $role = UserRole::CUSTOMER,
  ): string {
    if ($this->userRepository->emailExists($email)) {
      throw new UserAlreadyExistsException($email);
    }

    $user = User::createWithHashedPassword(
      email: $email,
      plainPassword: $password,
      firstName: $firstName,
      lastName: $lastName,
      role: $role,
    );

    $this->userRepository->save($user);

    return $this->jwtService->generateToken($user);
  }
}
