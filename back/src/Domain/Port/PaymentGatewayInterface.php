<?php

namespace App\Domain\Port;

use App\Domain\Payment\PaymentRequest;
use App\Domain\Payment\PaymentResult;
use App\Domain\Payment\PaymentStatus;
use App\Domain\Payment\RefundResult;
use App\Domain\Payment\CustomerData;

interface PaymentGatewayInterface
{
    /**
     * Crée une session de paiement (Checkout)
     */
    public function createPayment(PaymentRequest $request): PaymentResult;

    /**
     * Effectue un remboursement
     */
    public function refund(string $paymentId, int $amount): RefundResult;

    /**
     * Récupère le statut d'un paiement
     */
    public function getPaymentStatus(string $paymentId): PaymentStatus;

    /**
     * Crée un client sur la plateforme de paiement
     * @return string L'ID du client créé
     */
    public function createCustomer(CustomerData $customer): string;

    /**
     * Récupère un client par son ID
     */
    public function getCustomer(string $customerId): ?CustomerData;

    /**
     * Annule un paiement en attente
     */
    public function cancelPayment(string $paymentId): bool;
}
