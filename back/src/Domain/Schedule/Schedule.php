<?php

namespace App\Domain\Schedule;

use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: ScheduleId, openingDays: string, openingHours: string}>
 */
class Schedule extends Entity
{
  private function __construct(
    ScheduleId $id,
    string $openingDays,
    string $openingHours,
  ) {
    parent::__construct([
      "id" => $id,
      "openingDays" => $openingDays,
      "openingHours" => $openingHours,
    ]);
  }

  public static function create(
    string $openingDays,
    string $openingHours,
    ?ScheduleId $id = null,
  ): self {
    return new self(
      id: $id ?? ScheduleId::generate(),
      openingDays: $openingDays,
      openingHours: $openingHours,
    );
  }

  public function getId(): ScheduleId
  {
    return $this->props["id"];
  }

  public function getOpeningDays(): string
  {
    return $this->props["openingDays"];
  }

  public function getOpeningHours(): string
  {
    return $this->props["openingHours"];
  }
}
