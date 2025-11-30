<?php

namespace App\Domain\Rate;

use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: RateId, type: RateType, calculationRule: string, price: float, hourlyDiscount: ?float, duration: ?string}>
 */
class Rate extends Entity
{
  private function __construct(
    RateId $id,
    RateType $type,
    string $calculationRule,
    float $price,
    ?float $hourlyDiscount,
    ?string $duration,
  ) {
    parent::__construct([
      "id" => $id,
      "type" => $type,
      "calculationRule" => $calculationRule,
      "price" => $price,
      "hourlyDiscount" => $hourlyDiscount,
      "duration" => $duration,
    ]);
  }

  public static function create(
    RateType $type,
    string $calculationRule,
    float $price,
    ?float $hourlyDiscount = null,
    ?string $duration = null,
    ?RateId $id = null,
  ): self {
    return new self(
      id: $id ?? RateId::generate(),
      type: $type,
      calculationRule: $calculationRule,
      price: $price,
      hourlyDiscount: $hourlyDiscount,
      duration: $duration,
    );
  }

  public function getId(): RateId
  {
    return $this->props["id"];
  }

  public function getType(): RateType
  {
    return $this->props["type"];
  }

  public function getCalculationRule(): string
  {
    return $this->props["calculationRule"];
  }

  public function getPrice(): float
  {
    return $this->props["price"];
  }

  public function getHourlyDiscount(): ?float
  {
    return $this->props["hourlyDiscount"];
  }

  public function getDuration(): ?string
  {
    return $this->props["duration"];
  }
}
