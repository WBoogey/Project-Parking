<?php

namespace App\Domain\User\Application;

use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\Application\Exception\UserNotFoundException;

class DeleteUser
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository,
  ) {}

  public function execute(UserId $userId): void
  {
    $user = $this->userRepository->findById($userId);

    if ($user === null) {
      throw new UserNotFoundException("User not found");
    }

    $this->userRepository->delete($user);
  }
}
