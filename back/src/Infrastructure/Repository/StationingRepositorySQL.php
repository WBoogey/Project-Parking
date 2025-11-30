<?php



namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
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
                parking_id = :parking_id
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO stationings (id, start_time, end_time, status, user_id, parking_id)
              VALUES (:id, :start_time, :end_time, :status, :user_id, :parking_id)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $stationing->getId()->toString(),
      ":start_time" => $stationing->getStartTime()->format("Y-m-d H:i:s"),
      ":end_time" => $stationing->getEndTime()->format("Y-m-d H:i:s"),
      ":status" => $stationing->getStatus()->value,
      ":user_id" => $stationing->getUserId()->toString(),
      ":parking_id" => $stationing->getParkingId()->toString(),
    ]);
  }

  public function findById(StationingId $id): ?Stationing
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
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
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
            FROM stationings
            WHERE start_time >= :start_time AND end_time <= :end_time";
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
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
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
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
            FROM stationings
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateStationing($data),
      $results,
    );
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
      endTime: new DateTime($data["end_time"]),
      status: StationingStatus::from($data["status"]),
      userId: UserId::fromString($data["user_id"]),
      parkingId: ParkingId::fromString($data["parking_id"]),
      id: StationingId::fromString($data["id"]),
    );
  }
}
