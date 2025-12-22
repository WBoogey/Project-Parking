<?php

namespace App\Domain\Reservation;

enum ReservationStatus: string
{
    case PENDING = 'pending';       // Payment pending
    case CONFIRMED = 'confirmed';   // Payment completed or free (subscription)
    case CANCELLED = 'cancelled';   // Cancelled by user
    case REFUNDED = 'refunded';     // Cancelled and refunded
    case COMPLETED = 'completed';   // Reservation time has passed
}
