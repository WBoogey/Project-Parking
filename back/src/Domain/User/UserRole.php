<?php



namespace App\Domain\User;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case OWNER = 'owner';
    case ADMIN = 'admin';
}
