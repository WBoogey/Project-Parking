<?php



namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\SubscriptionId;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\User\UserId;
use PDO;

class SubscriptionRepositorySQL implements SubscriptionRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Subscription $subscription): void
  {
    $sql = "SELECT COUNT(*) FROM subscriptions WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $subscription->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE subscriptions SET
                user_id = :user_id,
                parking_id = :parking_id,
                start_date = :start_date,
                end_date = :end_date,
                rate = :rate,
                weekly_slots = :weekly_slots
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO subscriptions (id, user_id, parking_id, start_date, end_date, rate, weekly_slots)
              VALUES (:id, :user_id, :parking_id, :start_date, :end_date, :rate, :weekly_slots)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $subscription->getId()->toString(),
      ":user_id" => $subscription->getUserId()->toString(),
      ":parking_id" => $subscription->getParkingId()->toString(),
      ":start_date" => $subscription->getStartDate(),
      ":end_date" => $subscription->getEndDate(),
      ":rate" => $subscription->getRate(),
      ":weekly_slots" => json_encode($subscription->getWeeklySlots()),
    ]);
  }

  public function findById(SubscriptionId $id): ?Subscription
  {
    $sql = "SELECT id, user_id, parking_id, start_date, end_date, rate, weekly_slots
            FROM subscriptions
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateSubscription($data);
  }

  /**
   * @return Subscription[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT id, user_id, parking_id, start_date, end_date, rate, weekly_slots
            FROM subscriptions
            WHERE parking_id = :parking_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":parking_id" => $parkingId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSubscription($data),
      $results,
    );
  }

  /**
   * @return Subscription[]
   */
  public function findByUserId(UserId $userId): array
  {
    $sql = "SELECT id, user_id, parking_id, start_date, end_date, rate, weekly_slots
            FROM subscriptions
            WHERE user_id = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":user_id" => $userId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSubscription($data),
      $results,
    );
  }

  /**
   * @return Subscription[]
   */
  public function findByRate(float $rate): array
  {
    $sql = "SELECT id, user_id, parking_id, start_date, end_date, rate, weekly_slots
            FROM subscriptions
            WHERE rate = :rate";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":rate" => $rate]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
      fn(array $data) => $this->hydrateSubscription($data),
      $results,
    );
  }

  public function delete(Subscription $subscription): void
  {
    $sql = "DELETE FROM subscriptions WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $subscription->getId()->toString()]);
  }

  private function hydrateSubscription(array $data): Subscription
  {
    return Subscription::create(
      userId: UserId::fromString($data["user_id"]),
      parkingId: ParkingId::fromString($data["parking_id"]),
      startDate: $data["start_date"],
      endDate: $data["end_date"],
      rate: (float) $data["rate"],
      weeklySlots: json_decode($data["weekly_slots"], true) ?? [],
      id: SubscriptionId::fromString($data["id"]),
    );
  }
}
