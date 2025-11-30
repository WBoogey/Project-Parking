<?php

namespace App\Infrastructure\adaptaters;

use App\Domain\User\JwtServiceInterface;
use App\Domain\User\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class FirebaseJwtService implements JwtServiceInterface
{
  private readonly string $secret;
  private readonly int $expirationSeconds;
  private readonly string $algorithm;

  public function __construct(
    string $secret,
    int $expirationSeconds = 3600,
    string $algorithm = "HS256",
  ) {
    $this->secret = $secret;
    $this->expirationSeconds = $expirationSeconds;
    $this->algorithm = $algorithm;
  }

  public function generateToken(User $user): string
  {
    $now = time();

    $payload = [
      "iat" => $now,
      "exp" => $now + $this->expirationSeconds,
      "userId" => $user->getId()->toString(),
      "email" => $user->getEmail(),
      "role" => $user->getRole()->value,
    ];

    return JWT::encode($payload, $this->secret, $this->algorithm);
  }

  /**
   * @return array{userId: string, email: string, role: string, exp: int}|null
   */
  public function verifyToken(string $token): ?array
  {
    try {
      $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

      return [
        "userId" => $decoded->userId,
        "email" => $decoded->email,
        "role" => $decoded->role,
        "exp" => $decoded->exp,
      ];
    } catch (Throwable) {
      return null;
    }
  }
}
