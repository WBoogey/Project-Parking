<?php



namespace App\Domain\Customer\Application;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Reservation\Reservation;
use App\Domain\User\UserId;

class GetCustomerReservations
{
  public function __construct(
    private readonly CustomerRepositoryInterface $customerRepository,
  ) {}

  /** @return Reservation[] */
  public function execute(UserId $customerId): array
  {
    return $this->customerRepository->getReservations($customerId);
  }
}
