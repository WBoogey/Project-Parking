<?php

namespace App\Infrastructure\Core\Domain;

use App\Infrastructure\Core\Domain\Identifier;

/**
 * @template TProperties of array{id: Identifier}
 */
abstract class Entity
{
  /**
   * @var TProperties
   */
  protected readonly array $props;

  /**
   * @param TProperties $props
   */
  protected function __construct(array $props)
  {
    $this->props = $props;
  }

  public function getIdentifier(): Identifier
  {
    return $this->props["id"];
  }

  public function equals(?Entity $object): bool
  {
    if ($object === null) {
      return false;
    }

    if ($this === $object) {
      return true;
    }

    return $this->getIdentifier()->equals($object->getIdentifier());
  }
}
