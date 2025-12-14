<?php

namespace App\Infrastructure\Repository;
use App\Domain\Schedule\Schedule;
use App\Domain\Schedule\ScheduleRepositoryInterface;
use PDO;

class ScheduleRepositorySQL implements ScheduleRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Schedule $schedule): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO schedules (opening_days, opening_hours) VALUES (:opening_days, :opening_hours)'
        );
        $stmt->execute([
            ':opening_days' => $schedule->getOpeningDays(),
            ':opening_hours' => $schedule->getOpeningHours(),
        ]);
    }

    public function findById(int $id): ?Schedule
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM schedules WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Schedule(
                $row['id'],
                $row['opening_days'],
                $row['opening_hours']
            );
        }

        return null;
    }

    public function findByOpeningDays(string $openingDays): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM schedules WHERE opening_days = :opening_days'
        );
        $stmt->execute([':opening_days' => $openingDays]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];
        foreach ($results as $row) {
            $schedules[] = new Schedule(
                $row['id'],
                $row['opening_days'],
                $row['opening_hours']
            );
        }

        return $schedules;
    }

    public function findByOpeningHours(string $openingHours): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM schedules WHERE opening_hours = :opening_hours'
        );
        $stmt->execute([':opening_hours' => $openingHours]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];
        foreach ($results as $row) {
            $schedules[] = new Schedule(
                $row['id'],
                $row['opening_days'],
                $row['opening_hours']
            );
        }

        return $schedules;
    }

    public function findByParkingId(int $parkingId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT s.* FROM schedules s
             JOIN parking_schedules ps ON s.id = ps.schedule_id
             WHERE ps.parking_id = :parking_id'
        );
        $stmt->execute([':parking_id' => $parkingId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];
        foreach ($results as $row) {
            $schedules[] = new Schedule(
                $row['id'],
                $row['opening_days'],
                $row['opening_hours']
            );
        }

        return $schedules;
    }
    
    public function delete(Schedule $schedule): void
    {
        $stmt = $this->connection->prepare(
            'DELETE FROM schedules WHERE id = :id'
        );
        $stmt->execute([':id' => $schedule->getId()]);
    }
}