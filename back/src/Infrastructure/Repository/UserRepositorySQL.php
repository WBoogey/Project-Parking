<?php

namespace App\Infrastructure\Repository;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRole;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Parking\Parking;
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

class UserRepositorySQL implements UserRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(User $user): void
  {
    $sql = "SELECT COUNT(*) FROM users WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $user->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE users SET
                email = :email,
                password = :password,
                first_name = :first_name,
                last_name = :last_name,
                role = :role
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO users (id, email, password, first_name, last_name, role)
              VALUES (:id, :email, :password, :first_name, :last_name, :role)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $user->getId()->toString(),
      ":email" => $user->getEmail(),
      ":password" => $user->getPassword(),
      ":first_name" => $user->getFirstName(),
      ":last_name" => $user->getLastName(),
      ":role" => $user->getRole()->value,
    ]);
  }

  public function findById(UserId $id): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return hydrateUser($data);
  }

  public function findByEmail(string $email): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE email = :email";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":email" => $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return hydrateUser($data);
  }

  public function findByFullName(string $firstName, string $lastName): ?User
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE first_name = :first_name AND last_name = :last_name";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":first_name" => $firstName,
      ":last_name" => $lastName,
    ]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return hydrateUser($data);
  }

  /**
   * @return User[]
   */
  public function findByRole(UserRole $role): array
  {
    $sql = "SELECT id, email, password, first_name, last_name, role
            FROM users
            WHERE role = :role";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":role" => $role->value]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => hydrateUser($data), $results);
  }

  public function emailExists(string $email): bool
  {
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":email" => $email]);

    return (int) $stmt->fetchColumn() > 0;
  }

  public function delete(User $user): void
  {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $user->getId()->toString()]);
  }

  //Owner

  /**
   * @return Parking[]
   */
  public function getParkings(User $owner): array
  {
    $sql = "SELECT id, location, capacity, owner_id
            FROM parkings
            WHERE owner_id = :owner_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":owner_id" => $owner->getId()->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => Parking::create(
        location: $data["location"],
        capacity: (int) $data["capacity"],
        ownerId: UserId::fromString($data["owner_id"]),
        id: ParkingId::fromString($data["id"]),
      ),
      $results,
    );
  }

  public function addParkingToOwner(User $owner, Parking $parking): void
  {
    $sql = "UPDATE parkings SET owner_id = :owner_id WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $parking->getId()->toString(),
      ":owner_id" => $owner->getId()->toString(),
    ]);
  }

  public function removeParkingFromOwner(
    User $owner,
    ParkingId $parkingId,
  ): void {
    $sql =
      "UPDATE parkings SET owner_id = NULL WHERE id = :id AND owner_id = :owner_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $parkingId->toString(),
      ":owner_id" => $owner->getId()->toString(),
    ]);
  }

  //Customer

  /**
   * @return Reservation[]
   */
  public function getReservations(User $customer): array
  {
    $sql = "SELECT id, day_of_week, start_hour, end_hour, user_id, parking_id
            FROM reservations
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customer->getId()->toString()]);
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

  /**
   * @return Subscription[]
   */
  public function getSubscriptions(User $customer): array
  {
    $sql = "SELECT id, start_date, end_date, rate, weekly_slots, user_id, parking_id
            FROM subscriptions
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customer->getId()->toString()]);
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

  /**
   * @return Stationing[]
   */
  public function getStationings(User $customer): array
  {
    $sql = "SELECT id, start_time, end_time, status, user_id, parking_id
            FROM stationings
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $customer->getId()->toString()]);
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
