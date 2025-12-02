<?php

namespace App\Infrastructure\Repository;

use App\Domain\Owner\OwnerRepositoryInterface;
use App\Domain\User\UserId;
use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;
use PDO;

class OwnerRepositorySQL implements OwnerRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  /** @return Parking[] */
  public function getParkings(UserId $ownerId): array
  {
    $sql = "SELECT id, location, capacity, owner_id
            FROM parkings
            WHERE owner_id = :owner_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":owner_id" => $ownerId->toString()]);
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

  public function addParking(UserId $ownerId, Parking $parking): void
  {
    $sql = "INSERT INTO parkings (id, location, capacity, owner_id)
            VALUES (:id, :location, :capacity, :owner_id)";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $parking->getId()->toString(),
      ":location" => $parking->getLocation(),
      ":capacity" => $parking->getCapacity(),
      ":owner_id" => $ownerId->toString(),
    ]);
  }

  public function removeParking(UserId $ownerId, ParkingId $parkingId): void
  {
    $sql = "DELETE FROM parkings WHERE id = :id AND owner_id = :owner_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $parkingId->toString(),
      ":owner_id" => $ownerId->toString(),
    ]);
  }
}
