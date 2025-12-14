<?php

namespace App\Domain\Parking;

use App\Domain\Owner\Owner;
use App\Domain\Schedule\Schedule;

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

    private array $schedules;

    public function __construct(int $id, string $location, int $capacity, Owner $owner, array $schedules = [])
    {
        $this->id = $id;
        $this->location = $location;
        $this->capacity = $capacity;
        $this->owner = $owner;
        $this->schedules = $schedules;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getSchedules(): array
    {
        return $this->schedules;
    }
}
