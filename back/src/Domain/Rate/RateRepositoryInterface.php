<?php

declare(strict_types=1);

namespace App\Domain\Rate;

use App\Domain\Parking\ParkingId;

interface RateRepositoryInterface
{
  public function save(Rate $rate): void;

  public function findById(RateId $id): ?Rate;

  public function findByPrice(float $price): ?Rate;

  /**
   * @return Rate[]
   */
  public function findByType(RateType $type): array;

  /**
   * @return Rate[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Rate[]
   */
  public function findAll(): array;

  public function delete(Rate $rate): void;
}
