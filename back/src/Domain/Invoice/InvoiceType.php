<?php

namespace App\Domain\Invoice;

enum InvoiceType: string
{
    case RESERVATION = 'reservation';
    case STATIONING = 'stationing';
    case SUBSCRIPTION = 'subscription';
}
