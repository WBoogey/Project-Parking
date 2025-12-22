<?php

namespace App\Infrastructure\Core\Domain;

/**
 * @template T of array<string, mixed>
 */
abstract class ValueObject
{
  /**
   * @var T
   */
  protected readonly array $props;

  /**
   * @param T $props
   */
  public function __construct(array $props)
  {
    $this->props = $props;
  }

  /**
   * @return T
   */
  public function getProps(): array
  {
    return $this->props;
  }

  public function equals(?ValueObject $vo): bool
  {
    if ($vo === null) {
      return false;
    }

    return $this->props === $vo->props;
  }
}
