<?php

namespace App\HTTP;

use App\Infrastructure\Core\Config\Controllers;
use App\Services\UserService;
use App\Domain\User\Application\Exception\UserAlreadyExistsException;
use App\Domain\User\Application\Exception\InvalidCredentialsException;
use App\Domain\User\UserRole;

class UserController extends Controllers
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function signup(): bool|string
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->json(400, [
                "type" => "https://httpstatuses.com/400",
                "title" => "Bad Request",
                "detail" => "Invalid JSON body",
                "status" => 400,
            ]);
        }

        $email = $this->sanitize($input["email"] ?? "");
        $password = $input["password"] ?? "";
        $firstName = $this->sanitize($input["firstName"] ?? "");
        $lastName = $this->sanitize($input["lastName"] ?? "");
        $role = $input["role"] ?? "customer";

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Missing required fields: email, password, firstName, lastName",
                "status" => 422,
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Invalid email format",
                "status" => 422,
            ]);
        }

        $userRole = UserRole::tryFrom($role) ?? UserRole::CUSTOMER;

        try {
            $token = $this->userService->signup(
                email: $email,
                password: $password,
                firstName: $firstName,
                lastName: $lastName,
                role: $userRole,
            );

            return $this->success(
                data: ["token" => $token],
                message: "User registered successfully",
            );
        } catch (UserAlreadyExistsException $e) {
            return $this->json(409, [
                "type" => "https://httpstatuses.com/409",
                "title" => "Conflict",
                "detail" => $e->getMessage(),
                "status" => 409,
            ]);
        }
    }

    public function signin(): bool|string
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            return $this->json(400, [
                "type" => "https://httpstatuses.com/400",
                "title" => "Bad Request",
                "detail" => "Invalid JSON body",
                "status" => 400,
            ]);
        }

        $email = $this->sanitize($input["email"] ?? "");
        $password = $input["password"] ?? "";

        if (empty($email) || empty($password)) {
            return $this->json(422, [
                "type" => "https://httpstatuses.com/422",
                "title" => "Unprocessable Entity",
                "detail" => "Missing required fields: email, password",
                "status" => 422,
            ]);
        }

        try {
            $token = $this->userService->signin(
                email: $email,
                password: $password,
            );

            return $this->success(
                data: ["token" => $token],
                message: "Login successful",
            );
        } catch (InvalidCredentialsException $e) {
            return $this->json(401, [
                "type" => "https://httpstatuses.com/401",
                "title" => "Unauthorized",
                "detail" => "Invalid email or password",
                "status" => 401,
            ]);
        }
    }
}
