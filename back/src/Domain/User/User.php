<?php


namespace App\Domain\User;


class User
{
    private int $id;
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    
    public function __construct(int $id, string $email, string $password, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    // Vérification du mot de passe utilisateur
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}