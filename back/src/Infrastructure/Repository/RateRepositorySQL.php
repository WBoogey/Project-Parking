<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Parking\ParkingId;
use App\Domain\Rate\Rate;
use App\Domain\Rate\RateId;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\Rate\RateType;
use PDO;

class RateRepositorySQL implements RateRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Rate $rate): void
  {
    $sql = "SELECT COUNT(*) FROM rates WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $rate->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE rates SET
                parking_id = :parking_id,
                type = :type,
                calculation_rule = :calculation_rule,
                price = :price,
                hourly_discount = :hourly_discount,
                duration = :duration
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO rates (id, parking_id, type, calculation_rule, price, hourly_discount, duration)
              VALUES (:id, :parking_id, :type, :calculation_rule, :price, :hourly_discount, :duration)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $rate->getId()->toString(),
      ":parking_id" => $rate->getParkingId()->toString(),
      ":type" => $rate->getType()->value,
      ":calculation_rule" => $rate->getCalculationRule(),
      ":price" => $rate->getPrice(),
      ":hourly_discount" => $rate->getHourlyDiscount(),
      ":duration" => $rate->getDuration(),
    ]);
  }

  public function findById(RateId $id): ?Rate
  {
    $sql = "SELECT id, parking_id, type, calculation_rule, price, hourly_discount, duration
            FROM rates
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateRate($data);
  }

  public function findByPrice(float $price): ?Rate
  {
    $sql = "SELECT id, parking_id, type, calculation_rule, price, hourly_discount, duration
            FROM rates
            WHERE price = :price";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":price" => $price]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateRate($data);
  }

  /**
   * @return Rate[]
   */
  public function findByType(RateType $type): array
  {
    $sql = "SELECT id, parking_id, type, calculation_rule, price, hourly_discount, duration
            FROM rates
            WHERE type = :type";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":type" => $type->value]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => $this->hydrateRate($data), $results);
  }

  /**
   * @return Rate[]
   */
  public function findByParkingId(ParkingId $parkingId): array
  {
    $sql = "SELECT id, parking_id, type, calculation_rule, price, hourly_discount, duration
            FROM rates
            WHERE parking_id = :parking_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":parking_id" => $parkingId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => $this->hydrateRate($data), $results);
  }

  /**
   * @return Rate[]
   */
  public function findAll(): array
  {
    $sql = "SELECT id, parking_id, type, calculation_rule, price, hourly_discount, duration
            FROM rates";
    $stmt = $this->connection->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => $this->hydrateRate($data), $results);
  }

  public function delete(Rate $rate): void
  {
    $sql = "DELETE FROM rates WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $rate->getId()->toString()]);
  }

  private function hydrateRate(array $data): Rate
  {
    return Rate::create(
      parkingId: ParkingId::fromString($data["parking_id"]),
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
}
