<?php

namespace App\Domain\Rate;

interface RateRepositoryInterface
{
  public function save(Rate $rate): void;

  public function findById(RateId $id): ?Rate;

  public function findByPrice(float $price): ?Rate;

  /**
   * @return Rate[]
   */
  public function findByType(RateType $type): array;

  public function delete(Rate $rate): void;
}
