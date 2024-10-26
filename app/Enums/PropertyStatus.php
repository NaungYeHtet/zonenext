<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: string implements HasColor, HasLabel
{
    case Draft = 'Draft';
    case Posted = 'Posted';
    case SoldOut = 'Sold out';
    case Rent = 'Rent';
    case RentNSoldOut = 'Rent & Sold out';
    case Completed = 'Completed';

    public function getLabel(): string
    {
        return __($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Posted => 'primary',
            self::SoldOut => 'warning',
            self::Rent => 'warning',
            self::RentNSoldOut => 'success',
            self::Completed => 'success',
        };
    }
}
