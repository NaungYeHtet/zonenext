<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Draft = 'Draft';
    case Posted = 'Posted';
    case SoldOut = 'Sold out';
    case Rent = 'Rent';
    case RentNSoldOut = 'Rent & Sold out';
    case Completed = 'Completed';
}
