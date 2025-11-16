<?php

namespace App\Infrastructure\Repository;

use App\Domain\Rate\Rate;
use App\Domain\Rate\RateType;
use App\Domain\Rate\RateRepositoryInterface;
use PDO;

class RateRepositorySQL implements RateRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Rate $rate): void
    {
        $sql = "SELECT COUNT(*) FROM rates WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $rate->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE rates SET price = :price, type = :type WHERE id = :id";
        } else {
            $sql = "INSERT INTO rates (id, price, type) VALUES (:id, :price, :type)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $rate->getId(),
            ':price' => $rate->getPrice(),
            ':type' => $rate->getType()->value
        ]);
    }

    public function findById(int $id): ?Rate
    {
        $sql = "SELECT * FROM rates WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;

        return new Rate(
            $data['id'],
            RateType::from($data['type']),
            $data['calculation_rule'],
            (float)$data['price'],
            $data['hourly_discount'] ?? null,
            $data['duration'] ?? null
        );
    }

    public function findByPrice(float $price): Rate|null
    {
        $sql = "SELECT * FROM rates WHERE price = :price";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':price' => $price]);
        $data = $stmt->fetch();
        if (!$data) return null;

        return new Rate(
            $data['id'],
            RateType::from($data['type']),
            $data['calculation_rule'],
            (float)$data['price'],
            $data['hourly_discount'] ?? null,
            $data['duration'] ?? null
        );
    }

    public function findByType(RateType $type): array
    {
        $sql = "SELECT * FROM rates WHERE type = :type";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':type' => $type->value]);
        $results = $stmt->fetchAll();

        $rates = [];
        foreach ($results as $data) {
            $rates[] = new Rate(
                $data['id'],
                RateType::from($data['type']),
                $data['calculation_rule'],
                (float)$data['price'],
                $data['hourly_discount'] ?? null,
                $data['duration'] ?? null
            );
        }
        return $rates;
    }

    public function delete(Rate $rate): void
    {
        $sql = "DELETE FROM rates WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $rate->getId()]);
    }
}