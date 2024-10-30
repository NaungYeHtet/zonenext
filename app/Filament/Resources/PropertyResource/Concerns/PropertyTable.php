<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Models\Property;
use Filament\Tables;

trait PropertyTable
{
    public static function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->wrap(),
            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->formatStateUsing(fn (Property $record) => $record->type->getLabel().' | '.$record->acquisition_type->getLabel()),
            Tables\Columns\TextColumn::make('address')
                ->wrap(),
            Tables\Columns\TextColumn::make('price')
                ->label(__('Price'))
                ->formatStateUsing(fn (string $state) => $state)
                ->wrap(),
            Tables\Columns\TextColumn::make('purchased_price')
                ->label(__('Purchased price'))
                ->formatStateUsing(fn (string $state) => number_format_price($state))
                ->wrap(),
        ];
    }
}
