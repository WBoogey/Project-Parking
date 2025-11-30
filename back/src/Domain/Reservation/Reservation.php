<?php

namespace App\Domain\Reservation;

use App\Domain\Parking\ParkingId;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;

/**
 * @extends Entity<array{id: ReservationId, interval: TimeInterval, parkingId: ParkingId, userId: UserId}>
 */
class Reservation extends Entity
{
  private function __construct(
    ReservationId $id,
    TimeInterval $interval,
    ParkingId $parkingId,
    UserId $userId,
  ) {
    parent::__construct([
      "id" => $id,
      "interval" => $interval,
      "parkingId" => $parkingId,
      "userId" => $userId,
    ]);
  }

  public static function create(
    TimeInterval $interval,
    ParkingId $parkingId,
    UserId $userId,
    ?ReservationId $id = null,
  ): self {
    return new self(
      id: $id ?? ReservationId::generate(),
      interval: $interval,
      parkingId: $parkingId,
      userId: $userId,
    );
  }

  public function getId(): ReservationId
  {
    return $this->props["id"];
  }

  public function getInterval(): TimeInterval
  {
    return $this->props["interval"];
  }

  public function getParkingId(): ParkingId
  {
    return $this->props["parkingId"];
  }

  public function getUserId(): UserId
  {
    return $this->props["userId"];
  }
}
