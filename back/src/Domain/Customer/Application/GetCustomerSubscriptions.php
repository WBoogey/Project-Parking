<?php



namespace App\Domain\Customer\Application;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\User\UserId;

class GetCustomerSubscriptions
{
  public function __construct(
    private readonly CustomerRepositoryInterface $customerRepository,
  ) {}

  /** @return Subscription[] */
  public function execute(UserId $customerId): array
  {
    return $this->customerRepository->getSubscriptions($customerId);
  }
}
