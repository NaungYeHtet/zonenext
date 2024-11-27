<?php

namespace App\Enums;

enum AppointmentStatus:string
{
    case Pending = 'Pending';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function getLabel(): string
    {
        return __($this->value);
    }
}
