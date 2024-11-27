<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case New = 'New';
    case Assigned = 'Assigned';
    case Contacted = 'Contacted';
    case FollowedUp = 'Followed up';
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
            self::FollowedUp => 'primary',
            self::UnderNegotiation => 'primary',
            self::Converted => 'success',
            self::Closed => 'gray',
        };
    }

    public function getHexColor(): string|array|null
    {
        return match ($this) {
            self::New => '#6B7280',
            self::Assigned => '#3e32a8',
            self::Contacted => '#92a832',
            self::FollowedUp => '#fcf819',
            self::UnderNegotiation => '#fc7819',
            self::Converted => '#47ff56',
            self::Closed => '#004a06',
        };
    }

    public function getOrder(): int
    {
        return match ($this) {
            self::New => 1,
            self::Assigned => 2,
            self::Contacted => 3,
            self::FollowedUp => 4,
            self::UnderNegotiation => 5,
            self::Converted => 6,
            self::Closed => 7,
        };
    }
}
