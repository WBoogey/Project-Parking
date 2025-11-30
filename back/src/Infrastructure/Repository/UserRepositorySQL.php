<?php

namespace App\Infrastructure\Repository;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\UserRole;
use PDO;

class UserRepositorySQL implements UserRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(User $user): void
  {
    $sql = "SELECT COUNT(*) FROM users WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $user->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE users SET
                email = :email,
                password = :password,
                first_name = :first_name,
                last_name = :last_name,
                role = :role
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO users (id, email, password, first_name, last_name, role)
              VALUES (:id, :email, :password, :first_name, :last_name, :role)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $user->getId()->toString(),
      ":email" => $user->getEmail(),
      ":password" => $user->getPassword(),
      ":first_name" => $user->getFirstName(),
      ":last_name" => $user->getLastName(),
      ":role" => $user->getRole()->value,
    ]);
  }

  public function findById(UserId $id): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateUser($data);
  }

  public function findByFullName(string $firstName, string $lastName): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE first_name = :first_name AND last_name = :last_name";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":first_name" => $firstName,
      ":last_name" => $lastName,
    ]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateUser($data);
  }

  public function findByEmail(string $email): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE email = :email";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":email" => $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateUser($data);
  }

  /**
   * @return User[]
   */
  public function findByRole(UserRole $role): array
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE role = :role";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":role" => $role->value]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => $this->hydrateUser($data), $results);
  }

  public function emailExists(string $email): bool
  {
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":email" => $email]);

    return (int) $stmt->fetchColumn() > 0;
  }

  public function delete(User $user): void
  {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $user->getId()->toString()]);
  }

  private function hydrateUser(array $data): User
  {
    return User::create(
      email: $data["email"],
      password: $data["password"],
      firstName: $data["first_name"],
      lastName: $data["last_name"],
      role: UserRole::from($data["role"]),
      id: UserId::fromString($data["id"]),
    );
  }
}
