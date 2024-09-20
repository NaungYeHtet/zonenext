<?php

namespace App\Enums;

use App\Traits\HasOptions;

enum PropertyType: string
{
    use HasOptions;

    case MiniCondo = 'Mini condo';
    case Condo = 'Condo';
    case Apartment = 'Apartment';
    case Independent = 'Independent';
    case Commercial = 'Commercial';
    case Land = 'Land';
    case Storage = 'Storage';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
