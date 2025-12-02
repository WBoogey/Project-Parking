<?php



namespace App\Infrastructure\Repository;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\User\UserId;
use App\Domain\Parking\ParkingId;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingId;
use App\Domain\Stationing\StationingStatus;
use App\Domain\TimeInterval\TimeInterval;
use PDO;

class CustomerRepositorySQL implements CustomerRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  /** @return Reservation[] */
  public function getReservations(UserId $customerId): array
  {
    $sql = "SELECT id, day_of_week, start_hour, end_hour, user_id, parking_id
            FROM reservations
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customerId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => Reservation::create(
        interval: new TimeInterval(
          $data["day_of_week"],
          $data["start_hour"],
          $data["end_hour"],
        ),
        parkingId: ParkingId::fromString($data["parking_id"]),
        userId: UserId::fromString($data["user_id"]),
        id: ReservationId::fromString($data["id"]),
      ),
      $results,
    );
  }

  /** @return Subscription[] */
  public function getSubscriptions(UserId $customerId): array
  {
    $sql = "SELECT id, start_date, end_date, rate, weekly_slots, user_id, parking_id
            FROM subscriptions
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customerId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => Subscription::create(
        userId: UserId::fromString($data["user_id"]),
        parkingId: ParkingId::fromString($data["parking_id"]),
        startDate: $data["start_date"],
        endDate: $data["end_date"],
        rate: (float) $data["rate"],
        weeklySlots: json_decode($data["weekly_slots"] ?? "[]", true),
        id: SubscriptionId::fromString($data["id"]),
      ),
      $results,
    );
  }

  /** @return Stationing[] */
  public function getStationings(UserId $customerId): array
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
            FROM stationings
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customerId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => Stationing::create(
        startTime: new \DateTime($data["start_time"]),
        endTime: new \DateTime($data["end_time"]),
        status: StationingStatus::from($data["status"]),
        userId: UserId::fromString($data["user_id"]),
        parkingId: ParkingId::fromString($data["parking_id"]),
        id: StationingId::fromString($data["id"]),
      ),
      $results,
    );
  }
}
