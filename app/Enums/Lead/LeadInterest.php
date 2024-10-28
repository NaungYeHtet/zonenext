<?php

namespace App\Enums\Lead;

enum LeadInterest: string
{
    case Buying = 'Buying';
    case Selling = 'Selling';
    case Renting = 'Renting';
}
