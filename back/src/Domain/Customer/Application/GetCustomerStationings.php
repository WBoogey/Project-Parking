<?php



namespace App\Domain\Customer\Application;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Stationing\Stationing;
use App\Domain\User\UserId;

class GetCustomerStationings
{
  public function __construct(
    private readonly CustomerRepositoryInterface $customerRepository,
  ) {}

  /** @return Stationing[] */
  public function execute(UserId $customerId): array
  {
    return $this->customerRepository->getStationings($customerId);
  }
}
