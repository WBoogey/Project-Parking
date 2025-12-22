<?php

namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\Reservation\ReservationStatus;
use App\Domain\User\UserId;
use DateTime;
use PDO;

class ReservationRepositorySQL implements ReservationRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Reservation $reservation): void
  {
    $sql = "SELECT COUNT(*) FROM reservations WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $reservation->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE reservations SET
                user_id = :user_id,
                parking_id = :parking_id,
                start_time = :start_time,
                end_time = :end_time,
                status = :status,
                rate_id = :rate_id,
                amount = :amount,
                is_free = :is_free
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO reservations (id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free)
              VALUES (:id, :user_id, :parking_id, :start_time, :end_time, :status, :rate_id, :amount, :is_free)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $reservation->getId()->toString(),
      ":user_id" => $reservation->getUserId()->toString(),
      ":parking_id" => $reservation->getParkingId()->toString(),
      ":start_time" => $reservation->getStartTime()->format("Y-m-d H:i:s"),
      ":end_time" => $reservation->getEndTime()->format("Y-m-d H:i:s"),
      ":status" => $reservation->getStatus()->value,
      ":rate_id" => $reservation->getRateId()?->toString(),
      ":amount" => $reservation->getAmount(),
      ":is_free" => $reservation->isFree() ? 1 : 0,
    ]);
  }

  public function saveWithPayment(
    Reservation $reservation,
    string $stripeSessionId,
    string $stripePaymentStatus,
  ): void {
    $sql = "INSERT INTO reservations 
            (id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free, stripe_session_id, stripe_payment_status)
            VALUES 
            (:id, :user_id, :parking_id, :start_time, :end_time, :status, :rate_id, :amount, :is_free, :stripe_session_id, :stripe_payment_status)";

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $reservation->getId()->toString(),
      ":user_id" => $reservation->getUserId()->toString(),
      ":parking_id" => $reservation->getParkingId()->toString(),
      ":start_time" => $reservation->getStartTime()->format("Y-m-d H:i:s"),
      ":end_time" => $reservation->getEndTime()->format("Y-m-d H:i:s"),
      ":status" => $reservation->getStatus()->value,
      ":rate_id" => $reservation->getRateId()?->toString(),
      ":amount" => $reservation->getAmount(),
      ":is_free" => $reservation->isFree() ? 1 : 0,
      ":stripe_session_id" => $stripeSessionId,
      ":stripe_payment_status" => $stripePaymentStatus,
    ]);
  }

  public function updatePaymentStatus(
    ReservationId $reservationId,
    string $stripePaymentStatus,
    ?\DateTimeImmutable $paidAt = null,
  ): void {
    $entityStatus = $stripePaymentStatus === 'success' 
      ? ReservationStatus::CONFIRMED->value 
      : ReservationStatus::PENDING->value;

    $sql = "UPDATE reservations
            SET stripe_payment_status = :status, paid_at = :paid_at, status = :entity_status
            WHERE id = :id";

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $reservationId->toString(),
      ":status" => $stripePaymentStatus,
      ":paid_at" => $paidAt?->format("Y-m-d H:i:s"),
      ":entity_status" => $entityStatus,
    ]);
  }

  public function findByStripeSessionId(string $stripeSessionId): ?Reservation
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE stripe_session_id = :stripe_session_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":stripe_session_id" => $stripeSessionId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateReservation($data);
  }

  public function findById(ReservationId $id): ?Reservation
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateReservation($data);
  }

  /**
   * @return Reservation[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE parking_id = :parking_id
            ORDER BY start_time DESC";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":parking_id" => $parkingId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateReservation($data),
      $results,
    );
  }

  /**
   * @return Reservation[]
   */
  public function findByUserId(UserId $userId): array
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE user_id = :user_id
            ORDER BY start_time DESC";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateReservation($data),
      $results,
    );
  }

  /**
   * @return Reservation[]
   */
  public function findOverlapping(ParkingId $parkingId, DateTime $startTime, DateTime $endTime): array
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE parking_id = :parking_id
              AND status IN ('pending', 'confirmed')
              AND start_time < :end_time
              AND end_time > :start_time";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":parking_id" => $parkingId->toString(),
      ":start_time" => $startTime->format("Y-m-d H:i:s"),
      ":end_time" => $endTime->format("Y-m-d H:i:s"),
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateReservation($data),
      $results,
    );
  }

  public function countOverlappingConfirmed(ParkingId $parkingId, DateTime $startTime, DateTime $endTime): int
  {
    $sql = "SELECT COUNT(*) FROM reservations
            WHERE parking_id = :parking_id
              AND status = 'confirmed'
              AND start_time < :end_time
              AND end_time > :start_time";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":parking_id" => $parkingId->toString(),
      ":start_time" => $startTime->format("Y-m-d H:i:s"),
      ":end_time" => $endTime->format("Y-m-d H:i:s"),
    ]);

    return (int) $stmt->fetchColumn();
  }

  public function delete(Reservation $reservation): void
  {
    $sql = "DELETE FROM reservations WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $reservation->getId()->toString()]);
  }

  public function getStripeSessionId(ReservationId $reservationId): ?string
  {
    $sql = "SELECT stripe_session_id FROM reservations WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $reservationId->toString()]);
    $result = $stmt->fetchColumn();

    return $result ?: null;
  }

  public function updateRefundStatus(ReservationId $reservationId, \DateTimeImmutable $refundedAt): void
  {
    $sql = "UPDATE reservations 
            SET status = :status, stripe_payment_status = 'refunded', refunded_at = :refunded_at
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $reservationId->toString(),
      ":status" => ReservationStatus::REFUNDED->value,
      ":refunded_at" => $refundedAt->format("Y-m-d H:i:s"),
    ]);
  }

  private function hydrateReservation(array $data): Reservation
  {
    return Reservation::create(
      userId: UserId::fromString($data["user_id"]),
      parkingId: ParkingId::fromString($data["parking_id"]),
      startTime: new DateTime($data["start_time"]),
      endTime: new DateTime($data["end_time"]),
      status: ReservationStatus::from($data["status"]),
      rateId: $data["rate_id"] ? RateId::fromString($data["rate_id"]) : null,
      amount: $data["amount"] ? (int) $data["amount"] : null,
      isFree: (bool) ($data["is_free"] ?? false),
      id: ReservationId::fromString($data["id"]),
    );
  }
}
