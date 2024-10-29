<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case New = 'New';
    case Assigned = 'Assigned';
    case Contacted = 'Contacted';
    case Scheduled = 'Scheduled';
    case UnderNegotiation = 'Under negotiation';
    case Converted = 'Converted';
    case Closed = 'Closed';

    public function getLabel(): ?string
    {
        return __($this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'gray',
            self::Assigned => 'primary',
            self::Contacted => 'primary',
            self::Scheduled => 'primary',
            self::UnderNegotiation => 'primary',
            self::Converted => 'success',
            self::Closed => 'gray',
        };
    }
}
