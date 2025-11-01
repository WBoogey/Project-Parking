<?php

namespace App\Domain\Stationing;

enum StationingStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
}
