<?php

namespace App\Filament\Resources\LeadRenterResource\Pages;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Filament\Resources\LeadRenterResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadRenter extends CreateRecord
{
    protected static string $resource = LeadRenterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Filament::auth()->id();
        $data['status'] = LeadStatus::Assigned;
        $data['interest'] = LeadInterest::Renting;
        $data['is_owner'] = false;

        return $data;
    }
}
