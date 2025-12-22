<?php

namespace App\Services;

use App\Domain\Parking\ParkingId;
use App\Domain\Stationing\Application\EnterParking;
use App\Domain\Stationing\Application\ExitParking;
use App\Domain\Stationing\Application\ExitParkingResult;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingRepositoryInterface;
use App\Domain\User\UserId;

class StationingService
{
  public function __construct(
    private readonly EnterParking $enterParkingUseCase,
    private readonly ExitParking $exitParkingUseCase,
    private readonly StationingRepositoryInterface $stationingRepository,
  ) {}

  /**
   * Enter a parking
   * 
   * @throws \InvalidArgumentException if parking not found
   * @throws \RuntimeException if parking is full or user already has active stationing
   */
  public function enter(UserId $userId, ParkingId $parkingId): Stationing
  {
    return $this->enterParkingUseCase->execute($userId, $parkingId);
  }

  /**
   * Exit a parking
   * Returns the exit result with payment URL if payment is required
   * 
   * @throws \RuntimeException if no active stationing found
   */
  public function exit(UserId $userId, ParkingId $parkingId): ExitParkingResult
  {
    return $this->exitParkingUseCase->execute($userId, $parkingId);
  }

  /**
   * Get user's stationings history
   * 
   * @return Stationing[]
   */
  public function getUserStationings(UserId $userId): array
  {
    return $this->stationingRepository->findByUserId($userId);
  }

  /**
   * Get active stationing for user in a parking (if any)
   */
  public function getActiveStationing(UserId $userId, ParkingId $parkingId): ?Stationing
  {
    return $this->stationingRepository->findActiveByUserAndParking($userId, $parkingId);
  }
}
