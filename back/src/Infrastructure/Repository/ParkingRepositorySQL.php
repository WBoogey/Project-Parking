<?php



namespace App\Infrastructure\Repository;

use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\User\UserId;
use PDO;

class ParkingRepositorySQL implements ParkingRepositoryInterface
{
  public function __construct(private readonly PDO $connection) {}

  public function save(Parking $parking): void
  {
    $sql = "SELECT COUNT(*) FROM parkings WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $parking->getId()->toString()]);
    $exists = (int) $stmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "UPDATE parkings SET
                location = :location,
                capacity = :capacity,
                owner_id = :owner_id
              WHERE id = :id";
    } else {
      $sql = "INSERT INTO parkings (id, location, capacity, owner_id)
              VALUES (:id, :location, :capacity, :owner_id)";
    }

    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
      ":id" => $parking->getId()->toString(),
      ":location" => $parking->getLocation(),
      ":capacity" => $parking->getCapacity(),
      ":owner_id" => $parking->getOwnerId()->toString(),
    ]);
  }

  public function findById(ParkingId $id): ?Parking
  {
    $sql = "SELECT id, location, capacity, owner_id
            FROM parkings
            WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $id->toString()]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateParking($data);
  }

  public function findByLocation(string $location): ?Parking
  {
    $sql = "SELECT id, location, capacity, owner_id
            FROM parkings
            WHERE location = :location";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":location" => $location]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return null;
    }

    return $this->hydrateParking($data);
  }

  /**
   * @return Parking[]
   */
  public function findByOwnerId(UserId $ownerId): array
  {
    $sql = "SELECT id, location, capacity, owner_id
            FROM parkings
            WHERE owner_id = :owner_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":owner_id" => $ownerId->toString()]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $data) => $this->hydrateParking($data), $results);
  }

  public function delete(Parking $parking): void
  {
    $sql = "DELETE FROM parkings WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([":id" => $parking->getId()->toString()]);
  }

  private function hydrateParking(array $data): Parking
  {
    return Parking::create(
      location: $data["location"],
      capacity: (int) $data["capacity"],
      ownerId: UserId::fromString($data["owner_id"]),
      id: ParkingId::fromString($data["id"]),
    );
  }
}
