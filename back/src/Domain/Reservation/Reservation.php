<?php

namespace App\Domain\Reservation;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\User\UserId;
use App\Infrastructure\Core\Domain\Entity;
use DateTime;

/**
 * Reservation for a specific date/time slot in a parking
 * 
 * @extends Entity<array{id: ReservationId, userId: UserId, parkingId: ParkingId, startTime: DateTime, endTime: DateTime, status: ReservationStatus, rateId: ?RateId, amount: ?int, isFree: bool}>
 */
class Reservation extends Entity
{
  private function __construct(
    ReservationId $id,
    UserId $userId,
    ParkingId $parkingId,
    DateTime $startTime,
    DateTime $endTime,
    ReservationStatus $status,
    ?RateId $rateId,
    ?int $amount,
    bool $isFree,
  ) {
    parent::__construct([
      "id" => $id,
      "userId" => $userId,
      "parkingId" => $parkingId,
      "startTime" => $startTime,
      "endTime" => $endTime,
      "status" => $status,
      "rateId" => $rateId,
      "amount" => $amount,
      "isFree" => $isFree,
    ]);
  }

  public static function create(
    UserId $userId,
    ParkingId $parkingId,
    DateTime $startTime,
    DateTime $endTime,
    ReservationStatus $status = ReservationStatus::PENDING,
    ?RateId $rateId = null,
    ?int $amount = null,
    bool $isFree = false,
    ?ReservationId $id = null,
  ): self {
    return new self(
      id: $id ?? ReservationId::generate(),
      userId: $userId,
      parkingId: $parkingId,
      startTime: $startTime,
      endTime: $endTime,
      status: $status,
      rateId: $rateId,
      amount: $amount,
      isFree: $isFree,
    );
  }

  public function getId(): ReservationId
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

  public function getStartTime(): DateTime
  {
    return $this->props["startTime"];
  }

  public function getEndTime(): DateTime
  {
    return $this->props["endTime"];
  }

  public function getStatus(): ReservationStatus
  {
    return $this->props["status"];
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
   * Confirm the reservation (after payment or if free)
   */
  public function confirm(): self
  {
    return new self(
      id: $this->getId(),
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      startTime: $this->getStartTime(),
      endTime: $this->getEndTime(),
      status: ReservationStatus::CONFIRMED,
      rateId: $this->getRateId(),
      amount: $this->getAmount(),
      isFree: $this->isFree(),
    );
  }

  /**
   * Cancel the reservation
   */
  public function cancel(): self
  {
    return new self(
      id: $this->getId(),
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      startTime: $this->getStartTime(),
      endTime: $this->getEndTime(),
      status: ReservationStatus::CANCELLED,
      rateId: $this->getRateId(),
      amount: $this->getAmount(),
      isFree: $this->isFree(),
    );
  }

  /**
   * Mark as refunded
   */
  public function markAsRefunded(): self
  {
    return new self(
      id: $this->getId(),
      userId: $this->getUserId(),
      parkingId: $this->getParkingId(),
      startTime: $this->getStartTime(),
      endTime: $this->getEndTime(),
      status: ReservationStatus::REFUNDED,
      rateId: $this->getRateId(),
      amount: $this->getAmount(),
      isFree: $this->isFree(),
    );
  }

  /**
   * Get duration in hours
   */
  public function getDurationHours(): float
  {
    $diff = $this->getEndTime()->getTimestamp() - $this->getStartTime()->getTimestamp();
    return $diff / 3600;
  }
}
