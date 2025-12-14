<?php

namespace App\Domain\Stationing;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;
use DateTime;

/**
 * @extends Entity<array{id: StationingId, startTime: DateTime, endTime: DateTime, status: StationingStatus, userId: UserId, parkingId: ParkingId}>
 */
class Stationing extends Entity
{
  private function __construct(
    StationingId $id,
    DateTime $startTime,
    DateTime $endTime,
    StationingStatus $status,
    UserId $userId,
    ParkingId $parkingId,
  ) {
    parent::__construct([
      "id" => $id,
      "startTime" => $startTime,
      "endTime" => $endTime,
      "status" => $status,
      "userId" => $userId,
      "parkingId" => $parkingId,
    ]);
  }

  public static function create(
    DateTime $startTime,
    DateTime $endTime,
    StationingStatus $status,
    UserId $userId,
    ParkingId $parkingId,
    ?StationingId $id = null,
  ): self {
    return new self(
      id: $id ?? StationingId::generate(),
      startTime: $startTime,
      endTime: $endTime,
      status: $status,
      userId: $userId,
      parkingId: $parkingId,
    );
  }

  public function getId(): StationingId
  {
    return $this->props["id"];
  }

  public function getStartTime(): DateTime
  {
    return $this->props["startTime"];
  }

  public function getEndTime(): DateTime
  {
    return $this->props["endTime"];
  }

  public function getStatus(): StationingStatus
  {
    return $this->props["status"];
  }

  public function getUserId(): UserId
  {
    return $this->props["userId"];
  }

  public function getParkingId(): ParkingId
  {
    return $this->props["parkingId"];
  }
}
