<?php

namespace App\Enums;

use App\Traits\HasOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PropertyType: string implements HasColor, HasLabel
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

    public function getColor(): string
    {
        return match ($this) {
            self::MiniCondo => 'success',
            self::Condo => 'warning',
            self::Apartment => 'info',
            self::Independent => 'primary',
            self::Commercial => 'danger',
            self::Land => 'gray',
            self::Storage => 'gray',
        };
    }
}
