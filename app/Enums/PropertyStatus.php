<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: string implements HasColor, HasLabel
{
    case Draft = 'Draft';
    case Posted = 'Posted';
    case Purchased = 'Purchased';
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
            self::Purchased => 'success',
            self::Completed => 'gray',
        };
    }
}
