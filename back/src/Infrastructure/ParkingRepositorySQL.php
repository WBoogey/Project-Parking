<?php

namespace App\Infrastructure\Repository;

use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Owner\Owner;
use PDO;

class ParkingRepositorySQL implements ParkingRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Parking $parking): void
    {
        $sql = "SELECT COUNT(*) FROM parkings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $parking->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE parkings SET location = :location, capacity = :capacity, owner_id = :owner_id WHERE id = :id";
        } else {
            $sql = "INSERT INTO parkings (id, location, capacity, owner_id) VALUES (:id, :location, :capacity, :owner_id)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $parking->getId(),
            ':location' => $parking->getLocation(),
            ':capacity' => $parking->getCapacity(),
            ':owner_id' => $parking->getOwner()->getId()
        ]);
    }

    public function findById(int $id): ?Parking
    {
        $sql = "SELECT * FROM parkings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;
        // Hydratation simplifiée, à adapter selon Owner
        $owner = new Owner($data['owner_id'], '', '', '', ''); // à compléter
        return new Parking($data['id'], $data['location'], $data['capacity'], $owner);
    }

    public function findByOwner(Owner $owner): ?array
    {
        $sql = "SELECT * FROM parkings WHERE owner_id = :owner_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':owner_id' => $owner->getId()]);
        $results = $stmt->fetchAll();

        if (!$results) return null;

        $parkings = [];
        foreach ($results as $data) {
            $parkings[] = new Parking(
                $data['id'],
                $data['location'],
                $data['capacity'],
                $owner
            );
        }
        return $parkings;
    }

    public function findByLocation(string $location): ?Parking
    {
        $sql = "SELECT * FROM parkings WHERE location = :location";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':location' => $location]);
        $data = $stmt->fetch();
        if (!$data) return null;
        $owner = new Owner($data['owner_id'], '', '', '', ''); // à compléter
        return new Parking($data['id'], $data['location'], $data['capacity'], $owner);
    }

    public function delete(Parking $parking): void
    {
        $sql = "DELETE FROM parkings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $parking->getId()]);
    }
}