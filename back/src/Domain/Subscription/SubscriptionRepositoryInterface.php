<?php

namespace App\Domain\Subscription;

use App\Domain\Subscription\Subscription;

interface SubscriptionRepositoryInterface
{
    public function save(Subscription $subscription): void;

    /**
     * Récupérer un abonnement par son ID, parking, client ou prix
     */
    public function findById(int $id): ?Subscription;
    public function findByParking(Parking $parking): array;
    public function findByCustomer(Customer $customer): array;
    public function findByPrice(float $price): array;
    public function delete(Subscription $subscription): void;
}