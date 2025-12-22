<?php

declare(strict_types=1);

namespace App\Domain\Rate;

use App\Domain\Parking\ParkingId;
use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: RateId, parkingId: ParkingId, type: RateType, calculationRule: string, price: float, hourlyDiscount: ?float, duration: ?string}>
 */
class Rate extends Entity
{
  private function __construct(
    RateId $id,
    ParkingId $parkingId,
    RateType $type,
    string $calculationRule,
    float $price,
    ?float $hourlyDiscount,
    ?string $duration,
  ) {
    parent::__construct([
      "id" => $id,
      "parkingId" => $parkingId,
      "type" => $type,
      "calculationRule" => $calculationRule,
      "price" => $price,
      "hourlyDiscount" => $hourlyDiscount,
      "duration" => $duration,
    ]);
  }

  public static function create(
    ParkingId $parkingId,
    RateType $type,
    string $calculationRule,
    float $price,
    ?float $hourlyDiscount = null,
    ?string $duration = null,
    ?RateId $id = null,
  ): self {
    return new self(
      id: $id ?? RateId::generate(),
      parkingId: $parkingId,
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

  public function getParkingId(): ParkingId
  {
    return $this->props["parkingId"];
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

  public function updatePrice(float $price): self
  {
    return new self(
      id: $this->getId(),
      parkingId: $this->getParkingId(),
      type: $this->getType(),
      calculationRule: $this->getCalculationRule(),
      price: $price,
      hourlyDiscount: $this->getHourlyDiscount(),
      duration: $this->getDuration(),
    );
  }

  public function updateHourlyDiscount(?float $hourlyDiscount): self
  {
    return new self(
      id: $this->getId(),
      parkingId: $this->getParkingId(),
      type: $this->getType(),
      calculationRule: $this->getCalculationRule(),
      price: $this->getPrice(),
      hourlyDiscount: $hourlyDiscount,
      duration: $this->getDuration(),
    );
  }
}
