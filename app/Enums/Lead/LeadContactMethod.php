<?php

namespace App\Enums\Lead;

use App\Traits\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum LeadContactMethod: string implements HasLabel
{
    use HasOptions;

    case Phone = 'Phone';
    case Email = 'Email';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
