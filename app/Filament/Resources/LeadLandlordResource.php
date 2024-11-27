<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadBuyerResource\Widgets\AppointmentWidget;
use App\Filament\Resources\LeadLandlordResource\Pages;
use App\Models\Lead;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadLandlordResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): string
    {
        return __('Leads');
    }

    public static function getModelLabel(): string
    {
        return __('Landlords');
    }

    public static function getNavigationBadge(): ?string
    {
        return LeadResource::getNavigationBadgeQuery()->landlord()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return LeadResource::getNavigationBadgeQuery()->landlord()->count() > 10 ? 'warning' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return LeadResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return LeadResource::table($table)
            ->modifyQueryUsing(fn(Builder $query) => $query->landlord());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AppointmentWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeadLandlords::route('/'),
            'create' => Pages\CreateLeadLandlord::route('/create'),
            'edit' => Pages\EditLeadLandlord::route('/{record}/edit'),
        ];
    }
}
