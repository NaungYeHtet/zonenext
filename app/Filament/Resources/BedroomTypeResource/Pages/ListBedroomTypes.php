<?php

namespace App\Filament\Resources\BedroomTypeResource\Pages;

use App\Filament\Resources\BedroomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBedroomTypes extends ListRecords
{
    protected static string $resource = BedroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
