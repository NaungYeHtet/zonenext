<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadBuyerResource\Widgets\AppointmentWidget;
use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

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
