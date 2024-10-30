<?php

namespace App\Enums\Lead;

use App\Traits\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasLabel
{
    use HasOptions;

    case Buyers = 'Buyers';
    case Sellers = 'Sellers';
    case Renters = 'Renters';
    case Landloards = 'Landlords';

    public function getLabel(): string
    {
        return __($this->value);
    }

    public function getResource(): string
    {
        return match ($this) {
            self::Buyers => \App\Filament\Resources\LeadBuyerResource::class,
            self::Sellers => \App\Filament\Resources\LeadSellerResource::class,
            self::Renters => \App\Filament\Resources\LeadRenterResource::class,
            self::Landloards => \App\Filament\Resources\LeadLandlordResource::class,
        };
    }
}
