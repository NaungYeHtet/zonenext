<?php

namespace App\Filament\Resources\LeadRenterResource\Pages;

use App\Filament\Resources\LeadRenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadRenters extends ListRecords
{
    protected static string $resource = LeadRenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
