<?php

namespace App\Domain\Owner;

use App\Domain\Owner\Owner;

interface OwnerRepositoryInterface
{
    /**
     * Sauvegarder un propriétaire (création ou mise à jour)
     */
    public function save(Owner $owner): void;

    /**
     * Récupérer un propriétaire par son ID, email ou nom
     */
    public function findById(int $id): ?Owner;
    public function findByEmail(string $email): ?Owner;
    public function findByFirstName(string $firstName): ?Owner;
    public function findByLastName(string $lastName): ?Owner;

    /**
     * Supprimer un propriétaire du domaine
     */
    public function delete(Owner $owner): void;

    /**
     * Liste des parkings d'un propriétaire
     */
    public function getParkings(Owner $owner): array;

    /**
     * Ajouter? supprimer un parking à un propriétaire
     */
    public function addParkingToOwner(Owner $owner, Parking $parking): void;

    public function removeParkingFromOwner(Owner $owner, Parking $parking): void;

}