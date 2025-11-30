<?php

namespace App\Domain\User\Application\Exception;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = "Invalid credentials")
    {
        parent::__construct($message);
    }
}
