<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Application;

use App\Domain\Port\PaymentGatewayInterface;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationId;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\Reservation\ReservationStatus;
use App\Domain\User\UserId;
use DateTimeImmutable;

class CancelReservationResult
{
  public function __construct(
    public readonly Reservation $reservation,
    public readonly bool $wasRefunded,
    public readonly ?string $refundId,
    public readonly ?string $errorMessage,
  ) {}
}

class CancelReservation
{
  public function __construct(
    private readonly ReservationRepositoryInterface $reservationRepository,
    private readonly ?PaymentGatewayInterface $paymentGateway,
  ) {}

  /**
   * Cancel a reservation
   * - If reservation was paid (not free), attempt refund via Stripe
   * - Only the owner of the reservation can cancel it
   * 
   * @throws \InvalidArgumentException if reservation not found
   * @throws \RuntimeException if user is not the owner or reservation cannot be cancelled
   */
  public function execute(ReservationId $reservationId, UserId $userId): CancelReservationResult
  {
    $reservation = $this->reservationRepository->findById($reservationId);
    
    if ($reservation === null) {
      throw new \InvalidArgumentException("Reservation not found");
    }

    // Check ownership
    if (!$reservation->getUserId()->equals($userId)) {
      throw new \RuntimeException("You are not authorized to cancel this reservation");
    }

    // Check if reservation can be cancelled
    $status = $reservation->getStatus();
    if ($status === ReservationStatus::CANCELLED || $status === ReservationStatus::REFUNDED) {
      throw new \RuntimeException("Reservation is already cancelled");
    }

    if ($status === ReservationStatus::COMPLETED) {
      throw new \RuntimeException("Cannot cancel a completed reservation");
    }

    // If reservation was free, just cancel it
    if ($reservation->isFree()) {
      $cancelledReservation = $reservation->cancel();
      $this->reservationRepository->save($cancelledReservation);

      return new CancelReservationResult(
        reservation: $cancelledReservation,
        wasRefunded: false,
        refundId: null,
        errorMessage: null,
      );
    }

    // If reservation was pending payment, just cancel it
    if ($status === ReservationStatus::PENDING) {
      $cancelledReservation = $reservation->cancel();
      $this->reservationRepository->save($cancelledReservation);

      return new CancelReservationResult(
        reservation: $cancelledReservation,
        wasRefunded: false,
        refundId: null,
        errorMessage: null,
      );
    }

    // If reservation was confirmed (paid), attempt refund
    if ($status === ReservationStatus::CONFIRMED && !$reservation->isFree()) {
      $stripeSessionId = $this->reservationRepository->getStripeSessionId($reservationId);

      if ($stripeSessionId !== null && $this->paymentGateway !== null && $reservation->getAmount() !== null) {
        $refundResult = $this->paymentGateway->refund($stripeSessionId, $reservation->getAmount());

        if ($refundResult->success) {
          $refundedReservation = $reservation->markAsRefunded();
          $this->reservationRepository->save($refundedReservation);
          $this->reservationRepository->updateRefundStatus($reservationId, new DateTimeImmutable());

          return new CancelReservationResult(
            reservation: $refundedReservation,
            wasRefunded: true,
            refundId: $refundResult->refundId,
            errorMessage: null,
          );
        } else {
          // Refund failed, still cancel the reservation but note the error
          $cancelledReservation = $reservation->cancel();
          $this->reservationRepository->save($cancelledReservation);

          return new CancelReservationResult(
            reservation: $cancelledReservation,
            wasRefunded: false,
            refundId: null,
            errorMessage: $refundResult->errorMessage ?? "Refund failed",
          );
        }
      }
    }

    // Default: just cancel
    $cancelledReservation = $reservation->cancel();
    $this->reservationRepository->save($cancelledReservation);

    return new CancelReservationResult(
      reservation: $cancelledReservation,
      wasRefunded: false,
      refundId: null,
      errorMessage: null,
    );
  }
}
