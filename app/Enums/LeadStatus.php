<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Scheduled = 'scheduled';
    case UnderNegotiation = 'under negotiation';
    case Closed = 'closed';
}
