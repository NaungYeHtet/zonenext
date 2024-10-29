<?php

namespace App\Enums\Lead;

use App\Traits\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum LeadInterest: string implements HasLabel
{
    use HasOptions;

    case Buying = 'Buying';
    case Selling = 'Selling';
    case Renting = 'Renting';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
