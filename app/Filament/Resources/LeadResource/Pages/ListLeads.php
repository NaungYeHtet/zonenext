<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Enums\Lead\LeadInterest;
use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'buyers' => Tab::make(__('Buyers'))
                ->badge(Lead::query()->where('interest', LeadInterest::Buying)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('interest', LeadInterest::Buying)),
            'renters' => Tab::make(__('Renters'))
                ->badge(Lead::query()->where('interest', LeadInterest::Renting)->where('is_owner', false)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('interest', LeadInterest::Renting)->where('is_owner', false)),
            'landlords' => Tab::make(__('Landlords'))
                ->badge(Lead::query()->where('interest', LeadInterest::Renting)->where('is_owner', true)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('interest', LeadInterest::Renting)->where('is_owner', true)),
            'sellers' => Tab::make(__('Sellers'))
                ->badge(Lead::query()->where('interest', LeadInterest::Selling)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('interest', LeadInterest::Selling)),
        ];
    }
}
