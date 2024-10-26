<?php

namespace App\Filament\Resources;

use App\Enums\AreaType;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('General')
                            ->schema([
                                Infolists\Components\TextEntry::make('title'),
                                Infolists\Components\TextEntry::make('type')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('length_width')
                                    ->default(fn (Model $record) => $record->length.'x'.$record->width)
                                    ->visible(fn (Model $record) => $record->area_type === AreaType::LengthWidth),
                                Infolists\Components\TextEntry::make('area')
                                    ->formatStateUsing(fn (string $state, Model $record) => $state.' '.$record->area_unit->getLabel())
                                    ->visible(fn (Model $record) => $record->area_type === AreaType::Area),
                                Infolists\Components\TextEntry::make('address'),
                                Infolists\Components\TextEntry::make('lat_long')
                                    ->default(fn (Model $record) => $record->latitude.' | '.$record->longitude)
                                    ->url(fn (Model $record) => "https://www.google.com/maps/?q={$record->latitude},{$record->longitude}", true)
                                    ->color('primary'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Price')
                            ->schema([
                                Infolists\Components\Fieldset::make('Sell')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('sell_price')
                                            ->label(__('Price')),
                                        Infolists\Components\IconEntry::make('sell_negotiable')
                                            ->label(__('Negotiable'))
                                            ->boolean(),
                                        Infolists\Components\TextEntry::make('sell_commission_description')
                                            ->label(__('Commission')),
                                    ])
                                    ->visible(fn (Model $record) => $record->is_sellable),
                                Infolists\Components\Fieldset::make('Rent')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('rent_price')
                                            ->label(__('Price')),
                                        Infolists\Components\IconEntry::make('rent_negotiable')
                                            ->label(__('Negotiable'))
                                            ->boolean(),
                                        Infolists\Components\TextEntry::make('rent_commission_description')
                                            ->label(__('Commission')),
                                    ])
                                    ->visible(fn (Model $record) => $record->is_rentable),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Gallery')
                            ->schema([
                                Infolists\Components\ImageEntry::make('cover_image_url')
                                    ->label(__('Cover image')),
                                Infolists\Components\ImageEntry::make('gallery')
                                    ->label(__('Images'))
                                    ->height(100)
                                    ->stacked()
                                    ->overlap(2),
                            ]),
                    ])->columns(2),
            ])->columns(1);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label(__('Cover image')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
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
