<?php

namespace App\Infrastructure\Repository;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use PDO;

class UserRepositorySQL implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): void
    {
        $sql = "SELECT COUNT(*) FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $user->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE users SET email = :email, password = :password, first_name = :first_name, last_name = :last_name WHERE id = :id";
        } else {
            $sql = "INSERT INTO users (id, email, password, first_name, last_name) VALUES (:id, :email, :password, :first_name, :last_name)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $user->getId(),
            ':email' => $user->getEmail(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName()
        ]);
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new User($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function findByFullName(string $firstName, string $lastName): ?User
    {
        $sql = "SELECT * FROM users WHERE first_name = :first_name AND last_name = :last_name";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new User($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new User($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function delete(User $user): void
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $user->getId()]);
    }
}