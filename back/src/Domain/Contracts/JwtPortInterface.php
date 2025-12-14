<?php

namespace App\Domain\Contracts;

use App\Domain\User\User;

interface JwtPortInterface
{
  /**
   * Génère un token JWT pour un utilisateur
   */
  public function generateToken(User $user): string;

  /**
   * Vérifie et décode un token JWT
   * @return array{userId: string, email: string, role: string, exp: int}|null
   */
  public function verifyToken(string $token): ?array;
}
