<?php

namespace App\Enums;

enum PropertyType: string
{
    case MiniCondo = 'Mini condo';
    case Condo = 'Condo';
    case Apartment = 'Apartment';
    case Independent = 'Independent';
    case Commercial = 'Commercial';
    case Land = 'Land';
    case Storage = 'Storage';
}
