<?php

namespace App\Filament\Resources\LeadSellerResource\Pages;

use App\Filament\Resources\LeadBuyerResource\Widgets\AppointmentWidget;
use App\Filament\Resources\LeadSellerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadSellers extends ListRecords
{
    protected static string $resource = LeadSellerResource::class;

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
