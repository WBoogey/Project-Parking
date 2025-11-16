<?php

namespace App\Infrastructure\Repository;

use App\Domain\Subscription\Subscription;
use App\Domain\Parking\Parking;
use App\Domain\Customer\Customer;
use App\Domain\Owner\Owner;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use PDO;

class SubscriptionRepositorySQL implements SubscriptionRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function save(Subscription $subscription): void
    {
        $sql = "SELECT COUNT(*) FROM subscriptions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $subscription->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE subscriptions SET start_date = :start_date, end_date = :end_date, rate = :rate, weekly_slots = :weekly_slots, customer_id = :customer_id, parking_id = :parking_id WHERE id = :id";
        } else {
            $sql = "INSERT INTO subscriptions (id, start_date, end_date, rate, weekly_slots, customer_id, parking_id) VALUES (:id, :start_date, :end_date, :rate, :weekly_slots, :customer_id, :parking_id)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $subscription->getId(),
            ':start_date' => $subscription->getStartDate(),
            ':end_date' => $subscription->getEndDate(),
            ':rate' => $subscription->getRate(),
            ':weekly_slots' => json_encode($subscription->getWeeklySlots()),
            ':customer_id' => $subscription->getCustomer()->getId(),
            ':parking_id' => $subscription->getParking()->getId()
        ]);
    }

    public function findById(int $id): ?Subscription
    {
        $sql = "SELECT * FROM subscriptions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;

        $customer = new Customer($data['customer_id'], '', '', '', '');
        $owner = new Owner(0, '', '', '', '', []);
        $parking = new Parking($data['parking_id'], '', 0, $owner);

        return new Subscription(
            $data['id'],
            $customer,
            $parking,
            $data['start_date'],
            $data['end_date'],
            (float)$data['rate'],
            json_decode($data['weekly_slots'], true)
        );
    }

    public function findByParking(Parking $parking): array
    {
        $sql = "SELECT * FROM subscriptions WHERE parking_id = :parking_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':parking_id' => $parking->getId()]);
        $results = [];
        while ($data = $stmt->fetch()) {
            $customer = new Customer($data['customer_id'], '', '', '', '');
            $results[] = new Subscription(
                $data['id'],
                $customer,
                $parking,
                $data['start_date'],
                $data['end_date'],
                (float)$data['rate'],
                json_decode($data['weekly_slots'], true)
            );
        }
        return $results;
    }

    public function findByCustomer(Customer $customer): array
    {
        $sql = "SELECT * FROM subscriptions WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = [];
        while ($data = $stmt->fetch()) {
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);
            $results[] = new Subscription(
                $data['id'],
                $customer,
                $parking,
                $data['start_date'],
                $data['end_date'],
                (float)$data['rate'],
                json_decode($data['weekly_slots'], true)
            );
        }
        return $results;
    }

    public function findByPrice(float $price): array
    {
        $sql = "SELECT * FROM subscriptions WHERE rate = :rate";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':rate' => $price]);
        $results = [];
        while ($data = $stmt->fetch()) {
            $customer = new Customer($data['customer_id'], '', '', '', '');
            $owner = new Owner(0, '', '', '', '', []);
            $parking = new Parking($data['parking_id'], '', 0, $owner);
            $results[] = new Subscription(
                $data['id'],
                $customer,
                $parking,
                $data['start_date'],
                $data['end_date'],
                (float)$data['rate'],
                json_decode($data['weekly_slots'], true)
            );
        }
        return $results;
    }

    public function delete(Subscription $subscription): void
    {
        $sql = "DELETE FROM subscriptions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $subscription->getId()]);
    }
}