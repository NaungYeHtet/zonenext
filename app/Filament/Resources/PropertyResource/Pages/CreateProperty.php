<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = PropertyStatus::Draft;
        $data['square_feet'] = null;

        return $data;
    }
}
