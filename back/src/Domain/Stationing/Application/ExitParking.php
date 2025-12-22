<?php

declare(strict_types=1);

namespace App\Domain\Stationing\Application;

use App\Domain\Parking\ParkingId;
use App\Domain\Parking\ParkingRepositoryInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Port\PaymentGatewayInterface;
use App\Domain\Rate\RateRepositoryInterface;
use App\Domain\Rate\RateType;
use App\Domain\Stationing\Stationing;
use App\Domain\Stationing\StationingRepositoryInterface;
use App\Domain\Subscription\SubscriptionRepositoryInterface;
use App\Domain\User\UserId;
use DateTime;

class ExitParkingResult
{
  public function __construct(
    public readonly Stationing $stationing,
    public readonly bool $isFree,
    public readonly ?int $amount,
    public readonly ?string $checkoutUrl,
  ) {}
}

class ExitParking
{
  public function __construct(
    private readonly StationingRepositoryInterface $stationingRepository,
    private readonly ParkingRepositoryInterface $parkingRepository,
    private readonly SubscriptionRepositoryInterface $subscriptionRepository,
    private readonly RateRepositoryInterface $rateRepository,
    private readonly PaymentGatewayInterface $paymentGateway,
  ) {}

  /**
   * Exit the parking. If user has active subscription, it's free. Otherwise, create Stripe payment.
   * 
   * @throws \InvalidArgumentException if stationing not found
   * @throws \RuntimeException if no active stationing found
   */
  public function execute(UserId $userId, ParkingId $parkingId): ExitParkingResult
  {
    // Find active stationing for this user in this parking
    $stationing = $this->stationingRepository->findActiveByUserAndParking($userId, $parkingId);
    if ($stationing === null) {
      throw new \RuntimeException("No active stationing found for this user in this parking");
    }

    $endTime = new DateTime();

    // Check if user has active subscription for this parking
    $isFree = $this->hasActiveSubscription($userId, $parkingId);

    if ($isFree) {
      // Free exit - subscription covers it
      $updatedStationing = $stationing->exit($endTime, null, 0, true);
      $this->stationingRepository->save($updatedStationing);

      return new ExitParkingResult(
        stationing: $updatedStationing,
        isFree: true,
        amount: 0,
        checkoutUrl: null,
      );
    }

    // Calculate price based on hourly rate
    $amount = $this->calculatePrice($parkingId, $stationing->getStartTime(), $endTime);
    $hourlyRate = $this->getHourlyRate($parkingId);

    // Create Stripe payment
    $parking = $this->parkingRepository->findById($parkingId);

    $durationMinutes = (int) (($endTime->getTimestamp() - $stationing->getStartTime()->getTimestamp()) / 60);
    $hours = floor($durationMinutes / 60);
    $minutes = $durationMinutes % 60;
    $durationStr = $hours > 0 ? "{$hours}h{$minutes}min" : "{$minutes}min";

    $paymentResult = $this->paymentGateway->createPayment(new PaymentRequest(
      amount: $amount,
      currency: 'eur',
      description: "Stationnement {$parking->getLocation()} - Durée: {$durationStr}",
      customerId: null,
      metadata: [
        'type' => 'stationing',
        'stationing_id' => $stationing->getId()->toString(),
        'parking_id' => $parkingId->toString(),
        'user_id' => $userId->toString(),
        'duration_minutes' => $durationMinutes,
      ],
      successUrl: 'http://localhost:5173/stationing/success?session_id={CHECKOUT_SESSION_ID}',
      cancelUrl: 'http://localhost:5173/stationing/cancel',
    ));

    // Update stationing with exit info and payment pending status
    $updatedStationing = $stationing->exit($endTime, $hourlyRate?->getId(), $amount, false);

    // Save with payment info
    $this->stationingRepository->saveWithPayment(
      $updatedStationing,
      $paymentResult->paymentId,
      'pending',
    );

    return new ExitParkingResult(
      stationing: $updatedStationing,
      isFree: false,
      amount: $amount,
      checkoutUrl: $paymentResult->checkoutUrl,
    );
  }

  /**
   * Check if user has an active subscription covering the current date/time
   */
  private function hasActiveSubscription(UserId $userId, ParkingId $parkingId): bool
  {
    $subscriptions = $this->subscriptionRepository->findByUserId($userId);

    $today = new DateTime();
    $todayStr = $today->format('Y-m-d');
    $dayOfWeek = strtolower($today->format('l')); // monday, tuesday, etc.
    $currentHour = (int) $today->format('H');

    foreach ($subscriptions as $subscription) {
      // Check if subscription is for this parking
      if ($subscription->getParkingId()->toString() !== $parkingId->toString()) {
        continue;
      }

      // Check if subscription is active (date range)
      if ($subscription->getStartDate() > $todayStr || $subscription->getEndDate() < $todayStr) {
        continue;
      }

      // Check weekly slots if defined
      $weeklySlots = $subscription->getWeeklySlots();
      if (empty($weeklySlots)) {
        // No weekly slots = subscription covers all days/hours
        return true;
      }

      // Check if current day/time is in weekly slots
      foreach ($weeklySlots as $slot) {
        if (isset($slot['day']) && strtolower($slot['day']) === $dayOfWeek) {
          // Check hours if defined
          if (isset($slot['start_hour']) && isset($slot['end_hour'])) {
            $startHour = (int) $slot['start_hour'];
            $endHour = (int) $slot['end_hour'];
            if ($currentHour >= $startHour && $currentHour < $endHour) {
              return true;
            }
          } else {
            // No hours specified = all day
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
      // Default to 3€/hour if no hourly rate defined
      $pricePerHour = 3.00;
    } else {
      $pricePerHour = $hourlyRate->getPrice();
    }

    // Calculate duration in hours (round up to next hour)
    $durationSeconds = $endTime->getTimestamp() - $startTime->getTimestamp();
    $durationHours = ceil($durationSeconds / 3600);

    // Minimum 1 hour
    if ($durationHours < 1) {
      $durationHours = 1;
    }

    // Return amount in cents
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
