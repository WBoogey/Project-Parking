<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Application;

use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Port\PaymentGatewayInterface;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\Rate\RateType;
use App\Domain\Reservation\Reservation;
use App\Domain\Reservation\ReservationRepositoryInterface;
use App\Domain\Reservation\ReservationStatus;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\User\UserId;
use DateTime;

class CreateReservationResult
{
  public function __construct(
    public readonly Reservation $reservation,
    public readonly bool $isFree,
    public readonly ?string $checkoutUrl,
    public readonly ?string $stripeSessionId,
  ) {}
}

class CreateReservation
{
  public function __construct(
    private readonly ReservationRepositoryInterface $reservationRepository,
    private readonly ParkingRepositoryInterface $parkingRepository,
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
    private readonly RateRepositoryInterface $rateRepository,
    private readonly ?PaymentGatewayInterface $paymentGateway,
    private readonly string $successUrl = 'http://localhost:3000/reservation/success',
    private readonly string $cancelUrl = 'http://localhost:3000/reservation/cancel',
  ) {}

  /**
   * Create a reservation for a specific time slot
   * - Check if parking exists
   * - Check capacity (considering existing reservations + active stationings)
   * - Check if user has active subscription (free) or requires payment
   * 
   * @throws \InvalidArgumentException if parking not found or invalid dates
   * @throws \RuntimeException if parking is full for the requested time slot
   */
  public function execute(
    UserId $userId,
    ParkingId $parkingId,
    DateTime $startTime,
    DateTime $endTime,
  ): CreateReservationResult {
    // Validate dates
    if ($startTime >= $endTime) {
      throw new \InvalidArgumentException("Start time must be before end time");
    }

    if ($startTime < new DateTime()) {
      throw new \InvalidArgumentException("Cannot create reservation in the past");
    }

    // Check if parking exists
    $parking = $this->parkingRepository->findById($parkingId);
    if ($parking === null) {
      throw new \InvalidArgumentException("Parking not found");
    }

    // Check capacity for the requested time slot
    $overlappingCount = $this->reservationRepository->countOverlappingConfirmed($parkingId, $startTime, $endTime);
    if ($overlappingCount >= $parking->getCapacity()) {
      throw new \RuntimeException("Parking is full for the requested time slot");
    }

    // Check if user has active subscription for this parking
    $isFree = $this->hasActiveSubscription($userId, $parkingId, $startTime, $endTime);

    if ($isFree) {
      // Free reservation - subscription covers it
      $reservation = Reservation::create(
        userId: $userId,
        parkingId: $parkingId,
        startTime: $startTime,
        endTime: $endTime,
        status: ReservationStatus::CONFIRMED,
        rateId: null,
        amount: 0,
        isFree: true,
      );

      $this->reservationRepository->save($reservation);

      return new CreateReservationResult(
        reservation: $reservation,
        isFree: true,
        checkoutUrl: null,
        stripeSessionId: null,
      );
    }

    // Calculate price based on hourly rate
    $amount = $this->calculatePrice($parkingId, $startTime, $endTime);
    $hourlyRate = $this->getHourlyRate($parkingId);

    // Create reservation with pending status
    $reservation = Reservation::create(
      userId: $userId,
      parkingId: $parkingId,
      startTime: $startTime,
      endTime: $endTime,
      status: ReservationStatus::PENDING,
      rateId: $hourlyRate?->getId(),
      amount: $amount,
      isFree: false,
    );

    // If no payment gateway, save as pending
    if ($this->paymentGateway === null) {
      $this->reservationRepository->save($reservation);

      return new CreateReservationResult(
        reservation: $reservation,
        isFree: false,
        checkoutUrl: null,
        stripeSessionId: null,
      );
    }

    // Create Stripe payment session
    $durationHours = ceil(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600);
    $dateStr = $startTime->format('d/m/Y H:i');

    $paymentResult = $this->paymentGateway->createPayment(new PaymentRequest(
      amount: $amount,
      currency: 'eur',
      description: "Réservation {$parking->getLocation()} - {$dateStr} ({$durationHours}h)",
      customerId: null,
      metadata: [
        'type' => 'reservation',
        'reservation_id' => $reservation->getId()->toString(),
        'parking_id' => $parkingId->toString(),
        'user_id' => $userId->toString(),
        'start_time' => $startTime->format('Y-m-d H:i:s'),
        'end_time' => $endTime->format('Y-m-d H:i:s'),
      ],
      successUrl: $this->successUrl . '?session_id={CHECKOUT_SESSION_ID}',
      cancelUrl: $this->cancelUrl,
    ));

    // Save reservation with payment info
    $this->reservationRepository->saveWithPayment(
      $reservation,
      $paymentResult->paymentId,
      'pending',
    );

    return new CreateReservationResult(
      reservation: $reservation,
      isFree: false,
      checkoutUrl: $paymentResult->checkoutUrl,
      stripeSessionId: $paymentResult->paymentId,
    );
  }

  /**
   * Check if user has an active subscription covering the reservation period
   */
  private function hasActiveSubscription(UserId $userId, ParkingId $parkingId, DateTime $startTime, DateTime $endTime): bool
  {
    $subscriptions = $this->subscriptionRepository->findByUserId($userId);
    
    $startDateStr = $startTime->format('Y-m-d');
    $endDateStr = $endTime->format('Y-m-d');

    foreach ($subscriptions as $subscription) {
      // Check if subscription is for this parking
      if ($subscription->getParkingId()->toString() !== $parkingId->toString()) {
        continue;
      }

      // Check if subscription covers the reservation dates
      if ($subscription->getStartDate() > $startDateStr || $subscription->getEndDate() < $endDateStr) {
        continue;
      }

      // Check weekly slots if defined
      $weeklySlots = $subscription->getWeeklySlots();
      if (empty($weeklySlots)) {
        // No weekly slots = subscription covers all days/hours
        return true;
      }

      // Check if reservation day/time is in weekly slots
      $dayOfWeek = strtolower($startTime->format('l'));
      $startHour = (int) $startTime->format('H');
      $endHour = (int) $endTime->format('H');

      foreach ($weeklySlots as $slot) {
        if (isset($slot['day']) && strtolower($slot['day']) === $dayOfWeek) {
          if (isset($slot['start_hour']) && isset($slot['end_hour'])) {
            $slotStart = (int) $slot['start_hour'];
            $slotEnd = (int) $slot['end_hour'];
            if ($startHour >= $slotStart && $endHour <= $slotEnd) {
              return true;
            }
          } else {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Calculate price based on hourly rate
   * Returns amount in cents
   */
  private function calculatePrice(ParkingId $parkingId, DateTime $startTime, DateTime $endTime): int
  {
    $hourlyRate = $this->getHourlyRate($parkingId);
    
    if ($hourlyRate === null) {
      $pricePerHour = 3.00; // Default 3€/hour
    } else {
      $pricePerHour = $hourlyRate->getPrice();
    }

    $durationSeconds = $endTime->getTimestamp() - $startTime->getTimestamp();
    $durationHours = ceil($durationSeconds / 3600);

    if ($durationHours < 1) {
      $durationHours = 1;
    }

    return (int) ($durationHours * $pricePerHour * 100);
  }

  private function getHourlyRate(ParkingId $parkingId): ?\App\Domain\Rate\Rate
  {
    $rates = $this->rateRepository->findByParkingId($parkingId);
    
    foreach ($rates as $rate) {
      if ($rate->getType() === RateType::HOURLY) {
        return $rate;
      }
    }

    return null;
  }
}
