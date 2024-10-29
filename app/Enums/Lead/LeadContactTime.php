<?php

namespace App\Enums\Lead;

use App\Traits\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum LeadContactTime: string implements HasLabel
{
    use HasOptions;
    case Morning = 'Morning';
    case Afternoon = 'Afternoon';
    case Evening = 'Evening';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
