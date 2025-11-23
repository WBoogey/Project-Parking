<?php

namespace App\Infrastructure\Repository;

use App\Domain\TimeInterval\TimeInterval;
use App\Domain\TimeInterval\TimeIntervalRepositoryInterface;
use PDO;

class TimeIntervalRepository implements TimeIntervalRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(TimeInterval $timeInterval): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO timeintervals (day_of_week, start_hour, end_hour) VALUES (:day_of_week, :start_hour, :end_hour)'
        );
        $stmt->execute([
            ':day_of_week' => $timeInterval->getDayOfWeek(),
            ':start_hour' => $timeInterval->getStartHour(),
            ':end_hour' => $timeInterval->getEndHour(),
        ]);
    }

    public function findByDayOfWeek(string $dayOfWeek): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM timeintervals WHERE day_of_week = :day_of_week'
        );
        $stmt->execute([':day_of_week' => $dayOfWeek]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $timeIntervals = [];
        foreach ($results as $row) {
            $timeIntervals[] = new TimeInterval(
                $row['day_of_week'],
                $row['start_hour'],
                $row['end_hour']
            );
        }

        return $timeIntervals;
    }

    public function delete(TimeInterval $timeInterval): void
    {
        $stmt = $this->connection->prepare(
            'DELETE FROM timeintervals WHERE day_of_week = :day_of_week AND start_hour = :start_hour AND end_hour = :end_hour'
        );
        $stmt->execute([
            ':day_of_week' => $timeInterval->getDayOfWeek(),
            ':start_hour' => $timeInterval->getStartHour(),
            ':end_hour' => $timeInterval->getEndHour(),
        ]);
    }
}