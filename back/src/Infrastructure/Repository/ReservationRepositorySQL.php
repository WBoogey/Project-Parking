<?php



namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateType;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\User\UserId;
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
                day_of_week = :day_of_week,
                start_hour = :start_hour,
                end_hour = :end_hour,
                parking_id = :parking_id,
                user_id = :user_id
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO reservations (id, day_of_week, start_hour, end_hour, parking_id, user_id)
              VALUES (:id, :day_of_week, :start_hour, :end_hour, :parking_id, :user_id)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $reservation->getId()->toString(),
      ":day_of_week" => $reservation->getInterval()->getDayOfWeek(),
      ":start_hour" => $reservation->getInterval()->getStartHour(),
      ":end_hour" => $reservation->getInterval()->getEndHour(),
      ":parking_id" => $reservation->getParkingId()->toString(),
      ":user_id" => $reservation->getUserId()->toString(),
    ]);
  }

  public function findById(ReservationId $id): ?Reservation
  {
    $sql = "SELECT id, day_of_week, start_hour, end_hour, parking_id, user_id
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
  public function findByInterval(TimeInterval $interval): array
  {
    $sql = "SELECT id, day_of_week, start_hour, end_hour, parking_id, user_id
            FROM reservations
            WHERE day_of_week = :day_of_week
              AND start_hour = :start_hour
              AND end_hour = :end_hour";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":day_of_week" => $interval->getDayOfWeek(),
      ":start_hour" => $interval->getStartHour(),
      ":end_hour" => $interval->getEndHour(),
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateReservation($data),
      $results,
    );
  }

  /**
   * @return Reservation[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT id, day_of_week, start_hour, end_hour, parking_id, user_id
            FROM reservations
            WHERE parking_id = :parking_id";
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
    $sql = "SELECT id, day_of_week, start_hour, end_hour, parking_id, user_id
            FROM reservations
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateReservation($data),
      $results,
    );
  }

  public function getRateForReservation(Reservation $reservation): ?Rate
  {
    $sql = "SELECT r.id, r.type, r.calculation_rule, r.price, r.hourly_discount, r.duration
            FROM rates r
            INNER JOIN reservations res ON res.rate_id = r.id
            WHERE res.id = :reservation_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":reservation_id" => $reservation->getId()->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return Rate::create(
      type: RateType::from($data["type"]),
      calculationRule: $data["calculation_rule"],
      price: (float) $data["price"],
      hourlyDiscount: $data["hourly_discount"] !== null
        ? (float) $data["hourly_discount"]
        : null,
      duration: $data["duration"],
      id: RateId::fromString($data["id"]),
    );
  }

  public function delete(Reservation $reservation): void
  {
    $sql = "DELETE FROM reservations WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $reservation->getId()->toString()]);
  }

  private function hydrateReservation(array $data): Reservation
  {
    return Reservation::create(
      interval: new TimeInterval(
        $data["day_of_week"] ?? "",
        $data["start_hour"] ?? "",
        $data["end_hour"] ?? "",
      ),
      parkingId: ParkingId::fromString($data["parking_id"]),
      userId: UserId::fromString($data["user_id"]),
      id: ReservationId::fromString($data["id"]),
    );
  }
}
