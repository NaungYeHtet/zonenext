<?php

namespace App\Enums\Lead;

use App\Enums\PropertyAcquisitionType;
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

    public function getLeadType(bool $isOwner): LeadType
    {
        return match ($this) {
            self::Buying => LeadType::Buyers,
            self::Selling => LeadType::Sellers,
            self::Renting => $isOwner ? LeadType::Landloards : LeadType::Renters,
        };
    }

    public function getPropertyAcquisitionType(): PropertyAcquisitionType
    {
        return match ($this) {
            self::Buying => PropertyAcquisitionType::Sale,
            self::Selling => PropertyAcquisitionType::Sale,
            self::Renting => PropertyAcquisitionType::Rent,
        };
    }
}
