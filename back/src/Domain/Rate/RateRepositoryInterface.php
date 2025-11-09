<?php

namespace App\Domain\Rate;

use App\Domain\Rate\Rate;

interface RateRepositoryInterface
{
    /**
     * Sauvegarder un tarif (création ou mise à jour)
     */
    public function save(Rate $rate): void;

    /**
     * Récupérer un tarif par son ID, prix, type
     */
    public function findById(int $id): ?Rate;
    public function findByPrice(float $price): ?Rate;
    public function findByType(RateType $type): array;

    /**
     * Supprimer un tarif
     */
    public function delete(Rate $rate): void;
}