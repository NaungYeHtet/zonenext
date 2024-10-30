<?php

namespace App\Filament\Resources\LeadSellerResource\Pages;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Filament\Resources\LeadSellerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadSeller extends CreateRecord
{
    protected static string $resource = LeadSellerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Filament::auth()->id();
        $data['status'] = LeadStatus::Assigned;
        $data['interest'] = LeadInterest::Selling;
        $data['is_owner'] = true;

        return $data;
    }
}
