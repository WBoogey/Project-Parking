<?php

namespace App\Domain\User;

interface JwtServiceInterface
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
