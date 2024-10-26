<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyPriceType: string implements HasLabel
{
    case Range = 'Range';
    case Fix = 'Fix';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
