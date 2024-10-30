<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadRenterResource\Pages;
use App\Models\Lead;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadRenterResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): string
    {
        return __('Leads');
    }

    public static function getModelLabel(): string
    {
        return __('Renters');
    }

    public static function getNavigationBadge(): ?string
    {
        return LeadResource::getNavigationBadgeQuery()->renter()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return LeadResource::getNavigationBadgeQuery()->renter()->count() > 10 ? 'warning' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return LeadResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return LeadResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->renter());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeadRenters::route('/'),
            'create' => Pages\CreateLeadRenter::route('/create'),
            'edit' => Pages\EditLeadRenter::route('/{record}/edit'),
        ];
    }
}
