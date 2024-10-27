<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Concerns\PropertyAction;
use App\Filament\Resources\PropertyResource\Concerns\PropertyForm;
use App\Filament\Resources\PropertyResource\Concerns\PropertyInfolist;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    use PropertyAction, PropertyForm, PropertyInfolist;

    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(self::getInfolistSchema())->columns(1);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('address')
                    ->wrap(),
                Tables\Columns\TextColumn::make('price_detail')
                    ->label(__('Price'))
                    ->formatStateUsing(fn (string $state) => $state)
                    ->wrap(),
                Tables\Columns\TextColumn::make('sold_price')
                    ->label(__('Sold price'))
                    ->formatStateUsing(fn (string $state) => number_format_price($state))
                    ->wrap(),
                Tables\Columns\TextColumn::make('rented_price')
                    ->label(__('Rented price'))
                    ->formatStateUsing(fn (int $state) => $state == '' ? '' : number_format_price($state))
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions(self::getTableActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
