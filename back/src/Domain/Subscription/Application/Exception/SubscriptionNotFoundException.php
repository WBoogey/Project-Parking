<?php

namespace App\Domain\Subscription\Application\Exception;

use Exception;

class SubscriptionNotFoundException extends Exception
{
    public function __construct(string $message = "Subscription not found")
    {
        parent::__construct($message);
    }
}
