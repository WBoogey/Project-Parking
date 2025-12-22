<?php

namespace App\Domain\User\Application;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\Application\Exception\UserNotFoundException;

class GetUserProfile
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository,
  ) {}

  public function execute(UserId $userId): User
  {
    $user = $this->userRepository->findById($userId);

    if ($user === null) {
      throw new UserNotFoundException("User not found");
    }

    return $user;
  }
}
