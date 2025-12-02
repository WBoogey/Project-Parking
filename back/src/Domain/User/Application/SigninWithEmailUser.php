<?php

namespace App\Domain\User\Application;

use App\Domain\Port\JwtServiceInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\Application\Exception\InvalidCredentialsException;

class SigninWithEmailUser
{
  private readonly UserRepositoryInterface $userRepository;
  private readonly JwtServiceInterface $jwtService;

  public function __construct(
    UserRepositoryInterface $userRepository,
    JwtServiceInterface $jwtService,
  ) {
    $this->userRepository = $userRepository;
    $this->jwtService = $jwtService;
  }

  /**
   * @throws InvalidCredentialsException
   */
  public function execute(string $email, string $password): string
  {
    $user = $this->userRepository->findByEmail($email);

    if ($user === null) {
      throw new InvalidCredentialsException();
    }

    if (!$user->verifyPassword($password)) {
      throw new InvalidCredentialsException();
    }

    return $this->jwtService->generateToken($user);
  }
}
