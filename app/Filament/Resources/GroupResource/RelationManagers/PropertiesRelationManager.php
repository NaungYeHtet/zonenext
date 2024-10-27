<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Filament\Resources\PropertyResource\Concerns\PropertyTable;
use App\Models\Property;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PropertiesRelationManager extends RelationManager
{
    use PropertyTable;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Filament::auth()->user()->can('viewAny', Property::class) && $ownerRecord->type->getGroupableRelationship() == 'properties';
    }

    protected static string $relationship = 'properties';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns(self::getColumns())
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordTitle(fn (Model $record): string => "{$record->title}")
                    ->recordSelectSearchColumns(['title', 'description', 'slug'])
                    ->preloadRecordSelect()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
