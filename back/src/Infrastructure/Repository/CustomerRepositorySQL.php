<?php

namespace App\Infrastructure\Repository;

use App\Domain\Customer\Customer;
use App\Domain\Owner\Owner;
use App\Domain\Parking\Parking;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\Reservation\Reservation;
use App\Domain\Subscription\Subscription;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingStatus;
use App\Domain\Customer\CustomerRepositoryInterface;
use PDO;

class CustomerRepositorySQL implements CustomerRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Customer $customer): void
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':email' => $customer->getEmail()]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            // Update user (infos principales)
            $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id";
            $params = [
                ':id' => $existingId,
                ':first_name' => $customer->getFirstName(),
                ':last_name' => $customer->getLastName(),
            ];
            $this->connection->prepare($sql)->execute($params);

            // Update ou insert customer (liaison)
            $sql = "REPLACE INTO customers (id, user_id) VALUES (:id, :user_id)";
            $this->connection->prepare($sql)->execute([
                ':id' => $customer->getId(),
                ':user_id' => $existingId
            ]);
        } else {
            // Insert user
            $sql = "INSERT INTO users (email, password, first_name, last_name) VALUES (:email, :password, :first_name, :last_name)";
            $params = [
                ':email' => $customer->getEmail(),
                ':password' => $customer->getPassword(),
                ':first_name' => $customer->getFirstName(),
                ':last_name' => $customer->getLastName()
            ];
            $this->connection->prepare($sql)->execute($params);
            $userId = $this->connection->lastInsertId();

            // Insert customer
            $sql = "INSERT INTO customers (id, user_id) VALUES (:id, :user_id)";
            $this->connection->prepare($sql)->execute([
                ':id' => $customer->getId(),
                ':user_id' => $userId
            ]);
        }
    }

    public function findById(int $id): ?Customer
    {
        $sql = "SELECT u.email, u.password, u.first_name, u.last_name, c.id as customer_id
                FROM customers c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Customer(
            $data['customer_id'],
            $data['email'],
            $data['password'],
            $data['first_name'],
            $data['last_name']
        );
    }

    public function findByEmail(string $email): ?Customer
    {
        $sql = "SELECT u.email, u.password, u.first_name, u.last_name, c.id as customer_id
                FROM customers c
                INNER JOIN users u ON c.user_id = u.id
                WHERE u.email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Customer(
            $data['customer_id'],
            $data['email'],
            $data['password'],
            $data['first_name'],
            $data['last_name']
        );
    }

    public function findByFullName(string $firstName, string $lastName): ?Customer
    {
        $sql = "SELECT u.email, u.password, u.first_name, u.last_name, c.id as customer_id
                FROM customers c
                INNER JOIN users u ON c.user_id = u.id
                WHERE u.first_name = :first_name AND u.last_name = :last_name";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
        $data = $stmt->fetch();
        if (!$data) return null;
        return new Customer(
            $data['customer_id'],
            $data['email'],
            $data['password'],
            $data['first_name'],
            $data['last_name']
        );
    }

    public function delete(Customer $customer): void
    {
        $sql = "DELETE FROM customers WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $customer->getId()]);
    }

    public function getReservations(Customer $customer): array
    {
        $sql = "SELECT * FROM reservations WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = $stmt->fetchAll();

        $reservations = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);
            $interval = new TimeInterval(
                $data['day_of_week'] ?? '',
                $data['start_hour'] ?? '',
                $data['end_hour'] ?? ''
            );
            $reservations[] = new Reservation(
                $data['id'],
                $interval,
                $parking,
                $customer
            );
        }
        return $reservations;
    }

    public function getSubscriptions(Customer $customer): array
    {
        $sql = "SELECT * FROM subscriptions WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = $stmt->fetchAll();

        $subscriptions = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);

            $weeklySlots = !empty($data['weekly_slots'])
                ? json_decode($data['weekly_slots'], true)
                : [];

            $subscriptions[] = new Subscription(
                $data['id'],
                $customer,
                $parking,
                $data['start_date'],
                $data['end_date'],
                (float)$data['rate'],
                $weeklySlots
            );
        }
        return $subscriptions;
    }

    public function getStationings(Customer $customer): array
    {
        $sql = "SELECT * FROM stationings WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = $stmt->fetchAll();

        $stationings = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);

            $status = StationingStatus::from($data['status']);

            $stationings[] = new Stationing(
                $data['id'],
                new \DateTime($data['start_time']),
                new \DateTime($data['end_time']),
                $status,
                $customer,
                $parking
            );
        }
        return $stationings;
    }

}