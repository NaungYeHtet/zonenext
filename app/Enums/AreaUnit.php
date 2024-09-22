<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AreaUnit: string implements HasLabel
{
    case SquareFeet = 'Square feet';
    case Acre = 'Acre';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
