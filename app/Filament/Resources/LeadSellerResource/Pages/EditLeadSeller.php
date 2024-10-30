<?php

namespace App\Filament\Resources\LeadSellerResource\Pages;

use App\Filament\Resources\LeadSellerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadSeller extends EditRecord
{
    protected static string $resource = LeadSellerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
