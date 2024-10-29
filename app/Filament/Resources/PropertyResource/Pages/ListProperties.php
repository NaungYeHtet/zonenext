<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

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
            'purchased' => Tab::make(PropertyStatus::Purchased->getLabel())
                ->badge(Property::query()->where('status', PropertyStatus::Purchased)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PropertyStatus::Purchased))
                ->badgeColor(PropertyStatus::Purchased->getColor()),
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
