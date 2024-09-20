<?php

namespace App\Enums\Filters;

enum FilterListType: string
{
    case ForSale = 'for_sale';
    case ForRent = 'for_rent';
    case Newest = 'newest';
}
