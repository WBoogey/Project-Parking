<?php

namespace App\Infrastructure\Core\Domain;

use Ramsey\Uuid\Uuid;

abstract class Identifier extends ValueObject
{
  protected string $value;

  public function __construct(string $value)
  {
    $this->value = $value;
    parent::__construct(["value" => $value]);
  }

  /**
   * Génère un nouvel identifiant avec UUID v7
   */
  public static function generate(): static
  {
    return new static(Uuid::uuid7()->toString());
  }

  /**
   * Crée un identifiant à partir d'une chaîne existante
   */
  public static function fromString(string $value): static
  {
    return new static($value);
  }

  public function getValue(): string
  {
    return $this->value;
  }

  public function toString(): string
  {
    return $this->value;
  }

  public function __toString(): string
  {
    return $this->value;
  }

  public function equals(?ValueObject $other): bool
  {
    if ($other === null) {
      return false;
    }

    if (!$other instanceof static) {
      return false;
    }

    return $this->value === $other->value;
  }
}
