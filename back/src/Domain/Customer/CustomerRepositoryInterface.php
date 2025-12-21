<?php

namespace App\Domain\Customer;

use App\Domain\User\UserId;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;

    public function findById(int $id): ?Customer;
    public function findByEmail(string $email): ?Customer;
    public function findByFullName(string $firstName, string $lastName): ?Customer;

    public function delete(Customer $customer): void;

    public function getReservations(UserId $customerId): array;
    public function getSubscriptions(UserId $customerId): array;
    public function getStationings(UserId $customerId): array;
}
