<?php

namespace App\Domain\Subscription;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: SubscriptionId, userId: UserId, parkingId: ParkingId, startDate: string, endDate: string, rate: float, weeklySlots: array}>
 */
class Subscription extends Entity
{
  private function __construct(
    SubscriptionId $id,
    UserId $userId,
    ParkingId $parkingId,
    string $startDate,
    string $endDate,
    float $rate,
    array $weeklySlots,
  ) {
    parent::__construct([
      "id" => $id,
      "userId" => $userId,
      "parkingId" => $parkingId,
      "startDate" => $startDate,
      "endDate" => $endDate,
      "rate" => $rate,
      "weeklySlots" => $weeklySlots,
    ]);
  }

  public static function create(
    UserId $userId,
    ParkingId $parkingId,
    string $startDate,
    string $endDate,
    float $rate,
    array $weeklySlots = [],
    ?SubscriptionId $id = null,
  ): self {
    return new self(
      id: $id ?? SubscriptionId::generate(),
      userId: $userId,
      parkingId: $parkingId,
      startDate: $startDate,
      endDate: $endDate,
      rate: $rate,
      weeklySlots: $weeklySlots,
    );
  }

  public function getId(): SubscriptionId
  {
    return $this->props["id"];
  }

  public function getUserId(): UserId
  {
    return $this->props["userId"];
  }

  public function getParkingId(): ParkingId
  {
    return $this->props["parkingId"];
  }

  public function getStartDate(): string
  {
    return $this->props["startDate"];
  }

  public function getEndDate(): string
  {
    return $this->props["endDate"];
  }

  public function getRate(): float
  {
    return $this->props["rate"];
  }

  /**
   * @return array
   */
  public function getWeeklySlots(): array
  {
    return $this->props["weeklySlots"];
  }
}
