<?php

namespace App\Filament\Resources\LeadRenterResource\Pages;

use App\Filament\Resources\LeadRenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadRenter extends EditRecord
{
    protected static string $resource = LeadRenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
