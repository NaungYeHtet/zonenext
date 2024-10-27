<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TagType: string implements HasLabel
{
    case PropertyType = 'Property type';
    case ListingType = 'Listing type';
    case FeaturesAndAmenities = 'Features and amenities';
    case LocationSpecific = 'Location specific';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
