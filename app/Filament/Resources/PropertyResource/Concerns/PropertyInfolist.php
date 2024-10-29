<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Enums\AreaType;
use Filament\Infolists;
use Illuminate\Database\Eloquent\Model;

trait PropertyInfolist
{
    public static function getInfolistSchema(): array
    {
        return [
            Infolists\Components\Tabs::make()
                ->tabs([
                    Infolists\Components\Tabs\Tab::make('General')
                        ->schema([
                            Infolists\Components\TextEntry::make('title'),                                Infolists\Components\TextEntry::make('type')
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
                            Infolists\Components\TextEntry::make('tags.name')
                                ->badge()
                                ->label(__('Tags')),
                            Infolists\Components\TextEntry::make('views_count'),
                        ]),
                    Infolists\Components\Tabs\Tab::make('Price')
                        ->schema([
                            Infolists\Components\TextEntry::make('price')
                                ->label(__('Price')),
                            Infolists\Components\IconEntry::make('negotiable')
                                ->label(__('Negotiable'))
                                ->boolean(),
                            Infolists\Components\TextEntry::make('commission_description')
                                ->label(__('Commission')),
                        ])
                        ->columns(3),
                    Infolists\Components\Tabs\Tab::make('Gallery')
                        ->schema([
                            Infolists\Components\ImageEntry::make('cover_image'),
                            Infolists\Components\ImageEntry::make('images')
                                ->height(100),
                        ]),
                ])->columns(2),
        ];
    }
}
