<?php

namespace App\Filament\Resources\LeadBuyerResource\Pages;

use App\Filament\Resources\LeadBuyerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadBuyer extends EditRecord
{
    protected static string $resource = LeadBuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
