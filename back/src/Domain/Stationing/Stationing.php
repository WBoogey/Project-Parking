<?php

namespace App\Domain\Stationing;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;
use DateTime;

/**
 * @extends Entity<array{id: StationingId, startTime: DateTime, endTime: ?DateTime, status: StationingStatus, userId: UserId, parkingId: ParkingId, rateId: ?RateId, amount: ?int, isFree: bool}>
 */
class Stationing extends Entity
{
  private function __construct(
    StationingId $id,
    DateTime $startTime,
    ?DateTime $endTime,
    StationingStatus $status,
    UserId $userId,
    ParkingId $parkingId,
    ?RateId $rateId,
    ?int $amount,
    bool $isFree,
  ) {
    parent::__construct([
      "id" => $id,
      "startTime" => $startTime,
      "endTime" => $endTime,
      "status" => $status,
      "userId" => $userId,
      "parkingId" => $parkingId,
      "rateId" => $rateId,
      "amount" => $amount,
      "isFree" => $isFree,
    ]);
  }

  public static function create(
    DateTime $startTime,
    ?DateTime $endTime,
    StationingStatus $status,
    UserId $userId,
    ParkingId $parkingId,
    ?RateId $rateId = null,
    ?int $amount = null,
    bool $isFree = false,
    ?StationingId $id = null,
  ): self {
    return new self(
      id: $id ?? StationingId::generate(),
      startTime: $startTime,
      endTime: $endTime,
      status: $status,
      userId: $userId,
      parkingId: $parkingId,
      rateId: $rateId,
      amount: $amount,
      isFree: $isFree,
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

  public function getEndTime(): ?DateTime
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

  public function getRateId(): ?RateId
  {
    return $this->props["rateId"];
  }

  public function getAmount(): ?int
  {
    return $this->props["amount"];
  }

  public function isFree(): bool
  {
    return $this->props["isFree"];
  }

  /**
   * Exit the parking - set end time and calculate price
   */
  public function exit(DateTime $endTime, ?RateId $rateId, ?int $amount, bool $isFree): self
  {
    return new self(
      id: $this->getId(),
      startTime: $this->getStartTime(),
      endTime: $endTime,
      status: $isFree ? StationingStatus::COMPLETED : StationingStatus::COMPLETED,
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      rateId: $rateId,
      amount: $amount,
      isFree: $isFree,
    );
  }

  /**
   * Mark as paid after Stripe payment
   */
  public function markAsPaid(): self
  {
    return new self(
      id: $this->getId(),
      startTime: $this->getStartTime(),
      endTime: $this->getEndTime(),
      status: StationingStatus::PAID,
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      rateId: $this->getRateId(),
      amount: $this->getAmount(),
      isFree: $this->isFree(),
    );
  }
}
