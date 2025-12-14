<?php

namespace App\Infrastructure\Repository;

use App\Domain\Owner\Owner;
use App\Domain\Parking\Parking;
use App\Domain\Owner\OwnerRepositoryInterface;

use PDO;

class OwnerRepositorySQL implements OwnerRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Owner $owner): void
    {
        $sql = "SELECT COUNT(*) FROM owners WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $owner->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE owners SET email = :email, password = :password, first_name = :first_name, last_name = :last_name WHERE id = :id";
        } else {
            $sql = "INSERT INTO owners (id, email, password, first_name, last_name) VALUES (:id, :email, :password, :first_name, :last_name)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $owner->getId(),
            ':email' => $owner->getEmail(),
            ':first_name' => $owner->getFirstName(),
            ':last_name' => $owner->getLastName()
        ]);
    }

    public function findById(int $id): ?Owner
    {
        $sql = "SELECT * FROM owners WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Owner($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function findByEmail(string $email): ?Owner
    {
        $sql = "SELECT * FROM owners WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Owner($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function findByFullName(string $firstName, string $lastName): Owner|null
    {
        $sql = "SELECT * FROM owners WHERE first_name = :first_name AND last_name = :last_name";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Owner($data['id'], $data['email'], $data['password'], $data['first_name'], $data['last_name']);
    }

    public function delete(Owner $owner): void
    {
        $sql = "DELETE FROM owners WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $owner->getId()]);
    }

    public function getParkings(Owner $owner): array
    {
        $sql = "SELECT * FROM parkings WHERE owner_id = :owner_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':owner_id' => $owner->getId()]);
        $results = $stmt->fetchAll();

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

    public function addParkingToOwner(Owner $owner, Parking $parking): void
    {
        $sql = "UPDATE parkings SET owner_id = :owner_id WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $parking->getId(),
            ':owner_id' => $owner->getId()
        ]);
    }

    public function removeParkingFromOwner(Owner $owner, Parking $parking): void
    {
        $sql = "UPDATE parkings SET owner_id = NULL WHERE id = :id AND owner_id = :owner_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $parking->getId(),
            ':owner_id' => $owner->getId()
        ]);
    }

}