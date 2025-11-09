<?php

/**
 * Classe pour gérer plusieurs types de tarification (abonnement et ponctuel)
 */

namespace App\Domain\Rate;

use App\Domain\Rate\RateType;

class Rate
{
    private int $id;
    private RateType $type;
    private string $calculationRule;
    private float $price;
    
     /**
     * Réduction horaire applicable (en euros ou pourcentage selon la règle).
     * Peut être null s'il n'y a pas de remise sur le tarif horaire.
     */
    private ?float $hourlyDiscount;

    /**
     * Durée de validité du tarif (ex : '1 month', '1 week').
     * Null pour les tarifs ponctuels (hors abonnement).
     */
    private ?string $duration;

    public function __construct(
        int $id,
        RateType $type,
        string $calculationRule,
        float $price,
        ?float $hourlyDiscount = null,
        ?string $duration = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->calculationRule = $calculationRule;
        $this->price = $price;
        $this->hourlyDiscount = $hourlyDiscount;
        $this->duration = $duration;
    }
}