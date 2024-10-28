<?php

namespace App\Filament\Resources\BedroomTypeResource\Pages;

use App\Filament\Resources\BedroomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBedroomType extends EditRecord
{
    protected static string $resource = BedroomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
