<?php

namespace App\Infrastructure\Repository;

use App\Domain\Stationing\Stationing;
use App\Domain\Parking\Parking;
use App\Domain\Owner\Owner;
use App\Domain\Customer\Customer;
use App\Domain\Stationing\StationingStatus;
use App\Domain\Stationing\StationingRepositoryInterface;
use PDO;

class StationingRepositorySQL implements StationingRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Stationing $stationing): void
    {
        $sql = "SELECT COUNT(*) FROM stationings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $stationing->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE stationings SET start_time = :start_time, end_time = :end_time, status = :status, customer_id = :customer_id, parking_id = :parking_id WHERE id = :id";
        } else {
            $sql = "INSERT INTO stationings (id, start_time, end_time, status, customer_id, parking_id) VALUES (:id, :start_time, :end_time, :status, :customer_id, :parking_id)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $stationing->getId(),
            ':start_time' => $stationing->getStartTime()->format('Y-m-d H:i:s'),
            ':end_time' => $stationing->getEndTime()->format('Y-m-d H:i:s'),
            ':status' => $stationing->getStatus()->value,
            ':customer_id' => $stationing->getCustomer()->getId(),
            ':parking_id' => $stationing->getParking()->getId()
        ]);
    }

    public function findById(int $id): ?Stationing
    {
        $sql = "SELECT * FROM stationings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;

        $customer = new Customer($data['customer_id'], '', '', '', '');
        $owner = new Owner(0, '', '', '', '', []);
        $parking = new Parking($data['parking_id'], '', 0, $owner);

        return new Stationing(
            $data['id'],
            new \DateTime($data['start_time']),
            new \DateTime($data['end_time']),
            StationingStatus::from($data['status']),
            $customer,
            $parking
        );
    }

    public function findByInterval(\DateTime $startTime, \DateTime $endTime): array
    {
        $sql = "SELECT * FROM stationings WHERE start_time >= :start_time AND end_time <= :end_time";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':start_time' => $startTime->format('Y-m-d H:i:s'),
            ':end_time' => $endTime->format('Y-m-d H:i:s')
        ]);
        $results = $stmt->fetchAll();

        if (!$results) return [];

        $stationings = [];
        foreach ($results as $data) {
            $customer = new Customer($data['customer_id'], '', '', '', '');
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);

            $stationings[] = new Stationing(
                $data['id'],
                new \DateTime($data['start_time']),
                new \DateTime($data['end_time']),
                StationingStatus::from($data['status']),
                $customer,
                $parking
            );
        }

        return $stationings;
    }

    public function findByParking(Parking $parking): array
    {
        $sql = "SELECT * FROM stationings WHERE parking_id = :parking_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':parking_id' => $parking->getId()]);
        $results = $stmt->fetchAll();

        if (!$results) return [];

        $stationings = [];
        foreach ($results as $data) {
            $customer = new Customer($data['customer_id'], '', '', '', '');

            $stationings[] = new Stationing(
                $data['id'],
                new \DateTime($data['start_time']),
                new \DateTime($data['end_time']),
                StationingStatus::from($data['status']),
                $customer,
                $parking
            );
        }

        return $stationings;
    }

    public function findByCustomer(Customer $customer): array
    {
        $sql = "SELECT * FROM stationings WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = $stmt->fetchAll();

        if (!$results) return [];

        $stationings = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);

            $stationings[] = new Stationing(
                $data['id'],
                new \DateTime($data['start_time']),
                new \DateTime($data['end_time']),
                StationingStatus::from($data['status']),
                $customer,
                $parking
            );
        }

        return $stationings;
    }

    public function delete(Stationing $stationing): void
    {
        $sql = "DELETE FROM stationings WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $stationing->getId()]);
    }

}