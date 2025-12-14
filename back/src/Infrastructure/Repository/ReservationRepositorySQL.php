<?php

namespace App\Infrastructure\Repository;

use App\Domain\Reservation\Reservation;
use App\Domain\TimeInterval\TimeInterval;
use App\Domain\Parking\Parking;
use App\Domain\Customer\Customer;
use App\Domain\Owner\Owner;
use App\Domain\Rate\Rate;
use App\Domain\Reservation\ReservationRepositoryInterface;
use PDO;


class ReservationRepositorySQL implements ReservationRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Reservation $reservation): void
    {
        $sql = "SELECT COUNT(*) FROM reservations WHERE id = :id";
        $stmt = $this->connection->prepare($sql); 
        $stmt->execute([':id' => $reservation->getId()]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE reservations SET start_time = :start_time, end_time = :end_time, parking_id = :parking_id, customer_id = :customer_id WHERE id = :id";
        } else {
            $sql = "INSERT INTO reservations (id, start_time, end_time, parking_id, customer_id) VALUES (:id, :start_time, :end_time, :parking_id, :customer_id)";
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $reservation->getId(),
            ':interval' => $reservation->getInterval(),
            ':parking_id' => $reservation->getParking()->getId(),
            ':customer_id' => $reservation->getCustomer()->getId(),
        ]);
    }

    public function findById(int $id): ?Reservation
    {
        $sql = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        if (!$data) return null;

        $interval = new TimeInterval(
            $data['day_of_week'] ?? '',
            $data['start_hour'] ?? '',
            $data['end_hour'] ?? ''
        );

        $owner = new Owner(0, '', '', '', '', []); // à remplacer par une vraie récupération
        $parking = new Parking($data['parking_id'], '', 0, $owner);
        $customer = new Customer($data['customer_id'], '', '', '', '');

        return new Reservation(
            $data['id'],
            $interval,
            $parking,
            $customer
        );
    }

    public function findByInterval(TimeInterval $interval): array
    {
        $sql = "SELECT * FROM reservations WHERE day_of_week = :day_of_week AND start_hour = :start_hour AND end_hour = :end_hour";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':day_of_week' => $interval->getDayOfWeek(),
            ':start_hour' => $interval->getStartHour(),
            ':end_hour' => $interval->getEndHour()
        ]);
        $results = $stmt->fetchAll();

        $reservations = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []); // à remplacer par une vraie récupération
            $parking = new Parking($data['parking_id'], '', 0, $owner);
            $customer = new Customer($data['customer_id'], '', '', '', '');

            $reservations[] = new Reservation(
                $data['id'],
                $interval,
                $parking,
                $customer
            );
        }
        return $reservations;
    }

    public function findByParking(Parking $parking): array
    {
        $sql = "SELECT * FROM reservations WHERE parking_id = :parking_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':parking_id' => $parking->getId()]);
        $results = $stmt->fetchAll();

        $reservations = [];
        foreach ($results as $data) {
            $interval = new TimeInterval(
                $data['day_of_week'] ?? '',
                $data['start_hour'] ?? '',
                $data['end_hour'] ?? ''
            );
            $customer = new Customer($data['customer_id'], '', '', '', '');

            $reservations[] = new Reservation(
                $data['id'],
                $interval,
                $parking,
                $customer
            );
        }
        return $reservations;
    }

    public function findByCustomer(Customer $customer): array
    {
        $sql = "SELECT * FROM reservations WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':customer_id' => $customer->getId()]);
        $results = $stmt->fetchAll();

        $reservations = [];
        foreach ($results as $data) {
            $owner = new Owner(0, '', '', '', '', []); // à remplacer par une vraie récupération
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

    public function getRateForReservation(Reservation $reservation): ?Rate
    {
        $sql = "SELECT r.* FROM rates r
                INNER JOIN reservations res ON res.rate_id = r.id
                WHERE res.id = :reservation_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation->getId()]);
        $data = $stmt->fetch();
        if (!$data) return null;

        return new Rate(
            $data['id'],
            $data['type'],
            $data['calculation_rule'],
            (float)$data['price'],
            $data['hourly_discount'] ?? null,
            $data['duration'] ?? null
        );
    }
    public function delete(Reservation $reservation): void
    {
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $reservation->getId()]);
    }
}