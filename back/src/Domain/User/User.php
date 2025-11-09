<?php


namespace App\Domain\User;


class User
{
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    
    public function __construct(string $email, string $password, string $firstName, string $lastName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}