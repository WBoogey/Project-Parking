<?php

namespace App\Domain\Parking;

use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: ParkingId, location: string, capacity: int, ownerId: UserId}>
 */
class Parking extends Entity
{
  private function __construct(
    ParkingId $id,
    string $location,
    int $capacity,
    UserId $ownerId,
  ) {
    parent::__construct([
      "id" => $id,
      "location" => $location,
      "capacity" => $capacity,
      "ownerId" => $ownerId,
    ]);
  }

  public static function create(
    string $location,
    int $capacity,
    UserId $ownerId,
    ?ParkingId $id = null,
  ): self {
    return new self(
      id: $id ?? ParkingId::generate(),
      location: $location,
      capacity: $capacity,
      ownerId: $ownerId,
    );
  }

  public function getId(): ParkingId
  {
    return $this->props["id"];
  }

  public function getLocation(): string
  {
    return $this->props["location"];
  }

  public function getCapacity(): int
  {
    return $this->props["capacity"];
  }

  public function getOwnerId(): UserId
  {
    return $this->props["ownerId"];
  }
}
