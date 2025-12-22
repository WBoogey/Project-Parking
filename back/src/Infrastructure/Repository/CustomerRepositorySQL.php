<?php

namespace App\Infrastructure\Repository;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\User\UserId;
use App\Domain\Parking\ParkingId;
use App\Domain\Rate\RateId;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationStatus;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingId;
use App\Domain\Stationing\StationingStatus;
use PDO;

class CustomerRepositorySQL implements CustomerRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Customer $customer): void {}

  public function findById(int $id): ?Customer
  {
    return null;
  }

  public function findByEmail(string $email): ?Customer
  {
    return null;
  }

  public function findByFullName(string $firstName, string $lastName): ?Customer
  {
    return null;
  }

  public function delete(Customer $customer): void {}

  public function getReservations(UserId $customerId): array
  {
    $sql = "SELECT id, user_id, parking_id, start_time, end_time, status, rate_id, amount, is_free
            FROM reservations
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customerId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => Reservation::create(
        userId: UserId::fromString($data["user_id"]),
        parkingId: ParkingId::fromString($data["parking_id"]),
        startTime: new \DateTime($data["start_time"]),
        endTime: new \DateTime($data["end_time"]),
        status: ReservationStatus::from($data["status"]),
        rateId: $data["rate_id"] ? RateId::fromString($data["rate_id"]) : null,
        amount: $data["amount"] ? (int) $data["amount"] : null,
        isFree: (bool) $data["is_free"],
        id: ReservationId::fromString($data["id"]),
      ),
      $results,
    );
  }

  public function getSubscriptions(UserId $customerId): array
  {
    $sql = "SELECT id, start_date, end_date, rate_id, weekly_slots, user_id, parking_id
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
        rateId: RateId::fromString($data["rate_id"]),
        weeklySlots: json_decode($data["weekly_slots"] ?? "[]", true),
        id: SubscriptionId::fromString($data["id"]),
      ),
      $results,
    );
  }

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
        endTime: $data["end_time"] ? new \DateTime($data["end_time"]) : null,
        status: StationingStatus::from($data["status"]),
        userId: UserId::fromString($data["user_id"]),
        parkingId: ParkingId::fromString($data["parking_id"]),
        id: StationingId::fromString($data["id"]),
      ),
      $results,
    );
  }
}
