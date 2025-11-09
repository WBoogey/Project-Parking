<?php

namespace App\Domain\User;

use App\Domain\User\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    /**
     * Récupérer un utilisateur par son id, nom complet ou email
     */

    public function findById(int $id): ?User;

    public function findByFullName(string $firstName, string $lastName): ?User;

    public function findByEmail(string $email): ?User;

    public function delete(User $user): void;
}