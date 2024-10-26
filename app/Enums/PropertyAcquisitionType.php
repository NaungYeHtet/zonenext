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
}
