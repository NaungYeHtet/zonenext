<?php

namespace App\Filament\Resources\LeadLandlordResource\Pages;

use App\Filament\Resources\LeadBuyerResource\Widgets\AppointmentWidget;
use App\Filament\Resources\LeadLandlordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadLandlords extends ListRecords
{
    protected static string $resource = LeadLandlordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AppointmentWidget::make(),
        ];
    }
}
