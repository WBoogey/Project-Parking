<?php

namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingId;
use App\Domain\Stationing\StationingRepositoryInterface;
use App\Domain\Stationing\StationingStatus;
use App\Domain\User\UserId;
use DateTime;
use PDO;

class StationingRepositorySQL implements StationingRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Stationing $stationing): void
  {
    $sql = "SELECT COUNT(*) FROM stationings WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $stationing->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE stationings SET
                start_time = :start_time,
                end_time = :end_time,
                status = :status,
                user_id = :user_id,
                parking_id = :parking_id,
                rate_id = :rate_id,
                amount = :amount,
                is_free = :is_free
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO stationings (id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free)
              VALUES (:id, :start_time, :end_time, :status, :user_id, :parking_id, :rate_id, :amount, :is_free)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $stationing->getId()->toString(),
      ":start_time" => $stationing->getStartTime()->format("Y-m-d H:i:s"),
      ":end_time" => $stationing->getEndTime()?->format("Y-m-d H:i:s"),
      ":status" => $stationing->getStatus()->value,
      ":user_id" => $stationing->getUserId()->toString(),
      ":parking_id" => $stationing->getParkingId()->toString(),
      ":rate_id" => $stationing->getRateId()?->toString(),
      ":amount" => $stationing->getAmount(),
      ":is_free" => $stationing->isFree() ? 1 : 0,
    ]);
  }

  public function saveWithPayment(
    Stationing $stationing,
    string $stripeSessionId,
    string $stripePaymentStatus,
  ): void {
    $sql = "UPDATE stationings SET
              end_time = :end_time,
              status = :status,
              rate_id = :rate_id,
              amount = :amount,
              is_free = :is_free,
              stripe_session_id = :stripe_session_id,
              stripe_payment_status = :stripe_payment_status
            WHERE id = :id";

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $stationing->getId()->toString(),
      ":end_time" => $stationing->getEndTime()?->format("Y-m-d H:i:s"),
      ":status" => $stationing->getStatus()->value,
      ":rate_id" => $stationing->getRateId()?->toString(),
      ":amount" => $stationing->getAmount(),
      ":is_free" => $stationing->isFree() ? 1 : 0,
      ":stripe_session_id" => $stripeSessionId,
      ":stripe_payment_status" => $stripePaymentStatus,
    ]);
  }

  public function updatePaymentStatus(
    StationingId $stationingId,
    string $stripePaymentStatus,
    ?\DateTimeImmutable $paidAt = null,
  ): void {
    $sql = "UPDATE stationings
            SET stripe_payment_status = :status, paid_at = :paid_at, status = :entity_status
            WHERE id = :id";

    $entityStatus = $stripePaymentStatus === 'success' ? StationingStatus::PAID->value : StationingStatus::COMPLETED->value;

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $stationingId->toString(),
      ":status" => $stripePaymentStatus,
      ":paid_at" => $paidAt?->format("Y-m-d H:i:s"),
      ":entity_status" => $entityStatus,
    ]);
  }

  public function findByStripeSessionId(string $stripeSessionId): ?Stationing
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE stripe_session_id = :stripe_session_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":stripe_session_id" => $stripeSessionId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateStationing($data);
  }

  public function findById(StationingId $id): ?Stationing
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateStationing($data);
  }

  /**
   * @return Stationing[]
   */
  public function findByInterval(DateTime $startTime, DateTime $endTime): array
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE start_time >= :start_time AND (end_time <= :end_time OR end_time IS NULL)";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":start_time" => $startTime->format("Y-m-d H:i:s"),
      ":end_time" => $endTime->format("Y-m-d H:i:s"),
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateStationing($data),
      $results,
    );
  }

  /**
   * @return Stationing[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE parking_id = :parking_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":parking_id" => $parkingId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateStationing($data),
      $results,
    );
  }

  /**
   * @return Stationing[]
   */
  public function findByUserId(UserId $userId): array
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE user_id = :user_id
            ORDER BY start_time DESC";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateStationing($data),
      $results,
    );
  }

  public function findActiveByUserAndParking(UserId $userId, ParkingId $parkingId): ?Stationing
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id, rate_id, amount, is_free
            FROM stationings
            WHERE user_id = :user_id
              AND parking_id = :parking_id
              AND status = :status";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":user_id" => $userId->toString(),
      ":parking_id" => $parkingId->toString(),
      ":status" => StationingStatus::ACTIVE->value,
    ]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateStationing($data);
  }

  public function countActiveByParkingId(ParkingId $parkingId): int
  {
    $sql = "SELECT COUNT(*) FROM stationings
            WHERE parking_id = :parking_id AND status = :status";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":parking_id" => $parkingId->toString(),
      ":status" => StationingStatus::ACTIVE->value,
    ]);

    return (int) $stmt->fetchColumn();
  }

  public function delete(Stationing $stationing): void
  {
    $sql = "DELETE FROM stationings WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $stationing->getId()->toString()]);
  }

  private function hydrateStationing(array $data): Stationing
  {
    return Stationing::create(
      startTime: new DateTime($data["start_time"]),
      endTime: $data["end_time"] ? new DateTime($data["end_time"]) : null,
      status: StationingStatus::from($data["status"]),
      userId: UserId::fromString($data["user_id"]),
      parkingId: ParkingId::fromString($data["parking_id"]),
      rateId: $data["rate_id"] ? RateId::fromString($data["rate_id"]) : null,
      amount: $data["amount"] ? (int) $data["amount"] : null,
      isFree: (bool) ($data["is_free"] ?? false),
      id: StationingId::fromString($data["id"]),
    );
  }
}
