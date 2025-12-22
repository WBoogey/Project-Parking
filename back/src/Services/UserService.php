<?php

namespace App\Services;

use App\Domain\User\Application\SigninWithEmailUser;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\GetUserProfile;
use App\Domain\User\Application\UpdateUserProfile;
use App\Domain\User\Application\DeleteUser;
use App\Domain\User\Application\Exception\InvalidCredentialsException;
use App\Domain\User\Application\Exception\UserAlreadyExistsException;
use App\Domain\User\Application\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRole;

class UserService
{
  public function __construct(
    private readonly SignupWithEmailUser $signupUseCase,
    private readonly SigninWithEmailUser $signinUseCase,
    private readonly GetUserProfile $getUserProfileUseCase,
    private readonly UpdateUserProfile $updateUserProfileUseCase,
    private readonly DeleteUser $deleteUserUseCase,
  ) {}

  /**
   * @throws UserAlreadyExistsException
   */
  public function signup(
    string $email,
    string $password,
    string $firstName,
    string $lastName,
    UserRole $role = UserRole::CUSTOMER,
  ): string {
    return $this->signupUseCase->execute(
      email: $email,
      password: $password,
      firstName: $firstName,
      lastName: $lastName,
      role: $role,
    );
  }

  /**
   * @throws InvalidCredentialsException
   */
  public function signin(string $email, string $password): string
  {
    return $this->signinUseCase->execute(email: $email, password: $password);
  }

  /**
   * @throws UserNotFoundException
   */
  public function getProfile(UserId $userId): User
  {
    return $this->getUserProfileUseCase->execute($userId);
  }

  /**
   * @throws UserNotFoundException
   * @throws UserAlreadyExistsException
   */
  public function updateProfile(
    UserId $userId,
    ?string $email = null,
    ?string $firstName = null,
    ?string $lastName = null,
    ?string $password = null,
  ): User {
    return $this->updateUserProfileUseCase->execute(
      userId: $userId,
      email: $email,
      firstName: $firstName,
      lastName: $lastName,
      password: $password,
    );
  }

  /**
   * @throws UserNotFoundException
   */
  public function deleteUser(UserId $userId): void
  {
    $this->deleteUserUseCase->execute($userId);
  }
}
