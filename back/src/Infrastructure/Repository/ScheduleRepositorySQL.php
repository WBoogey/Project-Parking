<?php

namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Schedule\Schedule;
use App\Domain\Schedule\ScheduleId;
use App\Domain\Schedule\ScheduleRepositoryInterface;
use PDO;

class ScheduleRepositorySQL implements ScheduleRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Schedule $schedule): void
  {
    $sql = "SELECT COUNT(*) FROM schedules WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $schedule->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE schedules SET
                opening_days = :opening_days,
                opening_hours = :opening_hours
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO schedules (id, opening_days, opening_hours)
              VALUES (:id, :opening_days, :opening_hours)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $schedule->getId()->toString(),
      ":opening_days" => $schedule->getOpeningDays(),
      ":opening_hours" => $schedule->getOpeningHours(),
    ]);
  }

  public function findById(ScheduleId $id): ?Schedule
  {
    $sql = "SELECT id, opening_days, opening_hours
            FROM schedules
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateSchedule($data);
  }

  /**
   * @return Schedule[]
   */
  public function findByOpeningDays(string $openingDays): array
  {
    $sql = "SELECT id, opening_days, opening_hours
            FROM schedules
            WHERE opening_days = :opening_days";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":opening_days" => $openingDays]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSchedule($data),
      $results,
    );
  }

  /**
   * @return Schedule[]
   */
  public function findByOpeningHours(string $openingHours): array
  {
    $sql = "SELECT id, opening_days, opening_hours
            FROM schedules
            WHERE opening_hours = :opening_hours";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":opening_hours" => $openingHours]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSchedule($data),
      $results,
    );
  }

  /**
   * @return Schedule[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT s.id, s.opening_days, s.opening_hours
            FROM schedules s
            INNER JOIN parking_schedules ps ON s.id = ps.schedule_id
            WHERE ps.parking_id = :parking_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":parking_id" => $parkingId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSchedule($data),
      $results,
    );
  }

  public function delete(Schedule $schedule): void
  {
    $sql = "DELETE FROM schedules WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $schedule->getId()->toString()]);
  }

  private function hydrateSchedule(array $data): Schedule
  {
    return Schedule::create(
      openingDays: $data["opening_days"],
      openingHours: $data["opening_hours"],
      id: ScheduleId::fromString($data["id"]),
    );
  }
}
