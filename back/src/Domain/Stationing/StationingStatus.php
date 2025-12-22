<?php

namespace App\Domain\Stationing;

enum StationingStatus: string
{
    case ACTIVE = 'active';         // User is currently parked
    case COMPLETED = 'completed';   // User has exited, payment pending or free (subscription)
    case PAID = 'paid';             // Payment completed
}
