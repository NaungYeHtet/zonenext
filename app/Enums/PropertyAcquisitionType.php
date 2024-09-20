<?php

namespace App\Enums;

enum PropertyAcquisitionType: string
{
    case Sell = 'Sell';
    case Rent = 'Rent';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
