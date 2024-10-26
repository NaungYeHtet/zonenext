<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'draft' => Tab::make(PropertyStatus::Draft->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::Draft)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::Draft))
                ->badgeColor(PropertyStatus::Draft->getColor()),
            'posted' => Tab::make(PropertyStatus::Posted->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::Posted)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::Posted))
                ->badgeColor(PropertyStatus::Posted->getColor()),
            'sold_out' => Tab::make(PropertyStatus::SoldOut->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::SoldOut)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::SoldOut))
                ->badgeColor(PropertyStatus::SoldOut->getColor()),
            'rented' => Tab::make(PropertyStatus::Rented->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::Rented)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::Rented))
                ->badgeColor(PropertyStatus::Rented->getColor()),
            'completed' => Tab::make(PropertyStatus::Completed->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::Completed)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::Completed))
                ->badgeColor(PropertyStatus::Completed->getColor()),
            'trashed' => Tab::make(__('Trashed'))
                ->badge(Property::query()->onlyTrashed()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
                ->badgeColor('gray'),
        ];
    }
}
