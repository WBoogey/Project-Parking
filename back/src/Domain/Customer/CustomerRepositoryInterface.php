<?php

namespace App\Domain\Customer;

use App\Domain\Customer\Customer;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;

    /**
     * Récupérer un client par son ID, email, prénom ou nom
     */
    public function findById(int $id): ?Customer;
    public function findByEmail(string $email): ?Customer;
    public function findByFullName(string $firstName, string $lastName): ?Customer;

    /**
     * Supprimer un client du domaine
     */
    public function delete(Customer $customer): void;

    /**
     * Liste des réservations, abonnements ou stationnements d'un client
     */
    public function getReservations(Customer $customer): array;
    public function getSubscriptions(Customer $customer): array;
    public function getStationings(Customer $customer): array;
}
