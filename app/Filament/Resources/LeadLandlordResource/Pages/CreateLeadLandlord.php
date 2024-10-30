<?php

namespace App\Filament\Resources\LeadLandlordResource\Pages;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Filament\Resources\LeadLandlordResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadLandlord extends CreateRecord
{
    protected static string $resource = LeadLandlordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Filament::auth()->id();
        $data['status'] = LeadStatus::Assigned;
        $data['interest'] = LeadInterest::Renting;
        $data['is_owner'] = true;

        return $data;
    }
}
