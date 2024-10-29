<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource;
use App\Models\Admin;
use App\Models\Lead;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    public ?Lead $lead = null;

    public static function canAccess(array $parameters = []): bool
    {
        $leadId = request()->segment(3);
        if (! $leadId) {
            return false;
        }

        $lead = \App\Models\Lead::find($leadId);
        $authUser = Filament::auth()->user();

        return $authUser instanceof Admin && $authUser->can('createProperty', $lead);
    }

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
