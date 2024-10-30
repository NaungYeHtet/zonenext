<?php

namespace App\Filament\Resources\LeadBuyerResource\Pages;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Filament\Resources\LeadBuyerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadBuyer extends CreateRecord
{
    protected static string $resource = LeadBuyerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Filament::auth()->id();
        $data['status'] = LeadStatus::Assigned;
        $data['interest'] = LeadInterest::Buying;
        $data['is_owner'] = false;

        return $data;
    }
}
