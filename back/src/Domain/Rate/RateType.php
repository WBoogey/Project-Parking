<?php

namespace App\Domain\Rate;

enum RateType: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY_SUBSCRIPTION = 'weekly_subscription';
    case MONTHLY_SUBSCRIPTION = 'monthly_subscription';
    case YEARLY_SUBSCRIPTION = 'yearly_subscription';
}