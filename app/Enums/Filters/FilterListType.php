<?php

namespace App\Enums\Filters;

use App\Traits\HasOptions;

enum FilterListType: string
{
    use HasOptions;

    case ForSale = 'for_sale';
    case ForRent = 'for_rent';
    case Newest = 'newest';

    public function getLabel(): string
    {
        return __("filter.list_type.{$this->value}");
    }

    public function getFilterPriceMinimum(): int
    {
        return match ($this) {
            self::ForSale => 10000000,
            self::ForRent => 100000,
            self::Newest => 100000,
        };
    }

    public function getFilterPriceMaximum(): ?int
    {
        return match ($this) {
            self::ForSale => null,
            self::ForRent => 10000000,
            self::Newest => 10000000,
        };
    }
}
