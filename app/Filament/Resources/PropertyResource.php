<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Concerns\PropertyAction;
use App\Filament\Resources\PropertyResource\Concerns\PropertyForm;
use App\Filament\Resources\PropertyResource\Concerns\PropertyInfolist;
use App\Filament\Resources\PropertyResource\Concerns\PropertyTable;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Admin;
use App\Models\Property;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    use PropertyAction, PropertyForm, PropertyInfolist, PropertyTable;

    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'gmdi-house-o';

    public static function getModelLabel(): string
    {
        return __('Property');
    }

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
            ->schema(self::getPropertyForm())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $authUser = Filament::auth()->user();
                if ($authUser instanceof Admin && $authUser->hasRole('Agent')) {
                    $query->whereRelation('leads', 'admin_id', $authUser);
                }

                $query->orderBy('created_at', 'desc');
            })
            ->columns(self::getColumns())
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
