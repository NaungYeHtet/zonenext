<?php

namespace App\Enums;

enum PropertyAcquisitionType: string
{
    case Sale = 'Sale';
    case Rent = 'Rent';

    public function getLabel(): string
    {
        return __($this->value);
    }

    public function getSlug(): string
    {
        return match ($this) {
            self::Sale => 'for-sale',
            self::Rent => 'for-rent',
        };
    }
}
