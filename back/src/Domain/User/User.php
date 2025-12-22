<?php
namespace App\Domain\User;
use App\Infrastructure\Core\Domain\Entity;
/**
 * @extends Entity<array{id: UserId, email: string, password: string, firstName: string, lastName: string, role: UserRole}>
 */
class User extends Entity
{
  private function __construct(
    UserId $id,
    string $email,
    string $password,
    string $firstName,
    string $lastName,
    UserRole $role,
  ) {
    parent::__construct([
      "id" => $id,
      "email" => $email,
      "password" => $password,
      "firstName" => $firstName,
      "lastName" => $lastName,
      "role" => $role,
    ]);
  }
  public static function create(
    string $email,
    string $password,
    string $firstName,
    string $lastName,
    UserRole $role = UserRole::CUSTOMER,
    ?UserId $id = null,
  ): self {
    return new self(
      id: $id ?? UserId::generate(),
      email: $email,
      password: $password,
      firstName: $firstName,
      lastName: $lastName,
      role: $role,
    );
  }
  public static function createWithHashedPassword(
    string $email,
    string $plainPassword,
    string $firstName,
    string $lastName,
    UserRole $role = UserRole::CUSTOMER,
    ?UserId $id = null,
  ): self {
    return self::create(
      email: $email,
      password: password_hash($plainPassword, PASSWORD_BCRYPT),
      firstName: $firstName,
      lastName: $lastName,
      role: $role,
      id: $id,
    );
  }
  public function getId(): UserId
  {
    return $this->props["id"];
  }
  public function getEmail(): string
  {
    return $this->props["email"];
  }
  public function getFirstName(): string
  {
    return $this->props["firstName"];
  }
  public function getLastName(): string
  {
    return $this->props["lastName"];
  }
  public function getPassword(): string
  {
    return $this->props["password"];
  }
  public function getRole(): UserRole
  {
    return $this->props["role"];
  }
  public function isCustomer(): bool
  {
    return $this->props["role"] === UserRole::CUSTOMER;
  }
  public function isOwner(): bool
  {
    return $this->props["role"] === UserRole::OWNER;
  }
  public function isAdmin(): bool
  {
    return $this->props["role"] === UserRole::ADMIN;
  }
  public function verifyPassword(string $plainPassword): bool
  {
    return password_verify($plainPassword, $this->props["password"]);
  }
}
