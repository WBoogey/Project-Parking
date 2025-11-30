<?php



namespace App\Domain\Subscription;

use App\Domain\Parking\ParkingId;
use App\Domain\User\UserId;

interface SubscriptionRepositoryInterface
{
  public function save(Subscription $subscription): void;

  public function findById(SubscriptionId $id): ?Subscription;

  /**
   * @return Subscription[]
   */
  public function findByParkingId(ParkingId $parkingId): array;

  /**
   * @return Subscription[]
   */
  public function findByUserId(UserId $userId): array;

  /**
   * @return Subscription[]
   */
  public function findByRate(float $rate): array;

  public function delete(Subscription $subscription): void;
}
