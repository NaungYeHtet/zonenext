<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AreaType: string implements HasLabel
{
    case LengthWidth = 'Length width';
    case Area = 'Area';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
