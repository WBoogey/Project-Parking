<?php

namespace App\Services;

use App\Domain\User\Application\SigninWithEmailUser;
use App\Domain\User\Application\SignupWithEmailUser;
use App\Domain\User\Application\Exception\InvalidCredentialsException;
use App\Domain\User\Application\Exception\UserAlreadyExistsException;
use App\Domain\User\UserRole;

class UserService
{
    public function __construct(
        private readonly SignupWithEmailUser $signupUseCase,
        private readonly SigninWithEmailUser $signinUseCase,
    ) {}

    /**
     * @throws UserAlreadyExistsException
     */
    public function signup(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        UserRole $role = UserRole::CUSTOMER,
    ): string {
        return $this->signupUseCase->execute(
            email: $email,
            password: $password,
            firstName: $firstName,
            lastName: $lastName,
            role: $role,
        );
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function signin(string $email, string $password): string
    {
        return $this->signinUseCase->execute(
            email: $email,
            password: $password,
        );
    }
}
