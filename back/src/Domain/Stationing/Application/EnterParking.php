<?php

declare(strict_types=1);

namespace App\Domain\Stationing\Application;

use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingRepositoryInterface;
use App\Domain\Stationing\StationingStatus;
use App\Domain\User\UserId;
use DateTime;

class EnterParking
{
  public function __construct(
    private readonly StationingRepositoryInterface $stationingRepository,
    private readonly ParkingRepositoryInterface $parkingRepository,
  ) {}

  /**
   * @throws \InvalidArgumentException if parking not found
   * @throws \RuntimeException if parking is full or user already has active stationing
   */
  public function execute(UserId $userId, ParkingId $parkingId): Stationing
  {
    // Check if parking exists
    $parking = $this->parkingRepository->findById($parkingId);
    if ($parking === null) {
      throw new \InvalidArgumentException("Parking not found");
    }

    // Check if user already has an active stationing in this parking
    $existingStationing = $this->stationingRepository->findActiveByUserAndParking($userId, $parkingId);
    if ($existingStationing !== null) {
      throw new \RuntimeException("You already have an active stationing in this parking");
    }

    // Check parking capacity
    $activeCount = $this->stationingRepository->countActiveByParkingId($parkingId);
    if ($activeCount >= $parking->getCapacity()) {
      throw new \RuntimeException("Parking is full");
    }

    // Create new stationing with ACTIVE status
    $stationing = Stationing::create(
      startTime: new DateTime(),
      endTime: null,
      status: StationingStatus::ACTIVE,
      userId: $userId,
      parkingId: $parkingId,
    );

    $this->stationingRepository->save($stationing);

    return $stationing;
  }
}
