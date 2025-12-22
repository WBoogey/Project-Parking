<?php

namespace App\Infrastructure\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Infrastructure\Middleware\AuthContext;
use App\Infrastructure\Middleware\RequireCustomer;
use App\Services\CustomerService;

class CustomerController extends Controllers
{
    public function __construct(private readonly CustomerService $customerService) {}

    #[RequireCustomer]
    public function reservations(): bool|string
    {
        $user = AuthContext::getUser();
        $reservations = $this->customerService->getReservations($user->getId());

        $data = array_map(fn($reservation) => [
            'id' => $reservation->getId()->toString(),
            'startTime' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
            'endTime' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
            'status' => $reservation->getStatus()->value,
            'parkingId' => $reservation->getParkingId()->toString(),
            'userId' => $reservation->getUserId()->toString(),
            'rateId' => $reservation->getRateId()?->toString(),
            'amount' => $reservation->getAmount(),
            'isFree' => $reservation->isFree(),
        ], $reservations);

        return $this->success(data: $data, message: 'Customer reservations');
    }

    #[RequireCustomer]
    public function subscriptions(): bool|string
    {
        $user = AuthContext::getUser();
        $subscriptions = $this->customerService->getSubscriptions($user->getId());

        $data = array_map(fn($subscription) => [
            'id' => $subscription->getId()->toString(),
            'startDate' => $subscription->getStartDate(),
            'endDate' => $subscription->getEndDate(),
            'rate' => $subscription->getRate(),
            'weeklySlots' => $subscription->getWeeklySlots(),
            'parkingId' => $subscription->getParkingId()->toString(),
            'userId' => $subscription->getUserId()->toString(),
        ], $subscriptions);

        return $this->success(data: $data, message: 'Customer subscriptions');
    }

    #[RequireCustomer]
    public function stationings(): bool|string
    {
        $user = AuthContext::getUser();
        $stationings = $this->customerService->getStationings($user->getId());

        $data = array_map(fn($stationing) => [
            'id' => $stationing->getId()->toString(),
            'startTime' => $stationing->getStartTime()->format('Y-m-d H:i:s'),
            'endTime' => $stationing->getEndTime()?->format('Y-m-d H:i:s'),
            'status' => $stationing->getStatus()->value,
            'parkingId' => $stationing->getParkingId()->toString(),
            'userId' => $stationing->getUserId()->toString(),
        ], $stationings);

        return $this->success(data: $data, message: 'Customer stationings');
    }
}
