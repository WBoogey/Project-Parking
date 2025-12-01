<?php

namespace App\Domain\User;

use App\Domain\Parking\Parking;
use App\Domain\Parking\ParkingId;
use App\Domain\Reservation\Reservation;
use App\Domain\Subscription\Subscription;
use App\Domain\Stationing\Stationing;

interface UserRepositoryInterface
{

  public function save(User $user): void;

  public function findById(UserId $id): ?User;

  public function findByEmail(string $email): ?User;

  public function findByFullName(string $firstName, string $lastName): ?User;

  /**
   * @return User[]
   */
  public function findByRole(UserRole $role): array;

  public function emailExists(string $email): bool;

  public function delete(User $user): void;

  //Owner

  /**
   * @return Parking[]
   */
  public function getParkings(User $owner): array;

  public function addParkingToOwner(User $owner, Parking $parking): void;

  public function removeParkingFromOwner(User $owner, ParkingId $parkingId): void;

  //Customer

  /**
   * @return Reservation[]
   */
  public function getReservations(User $customer): array;

  /**
   * @return Subscription[]
   */
  public function getSubscriptions(User $customer): array;

  /**
   * @return Stationing[]
   */
  public function getStationings(User $customer): array;
}
