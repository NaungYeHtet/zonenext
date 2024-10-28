<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BedroomTypeResource\Pages;
use App\Models\BedroomType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BedroomTypeResource extends Resource
{
    protected static ?string $model = BedroomType::class;

    protected static ?string $navigationIcon = 'gmdi-bed-o';

    public static function getModelLabel(): string
    {
        return __('Bedroom type');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->translatable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBedroomTypes::route('/'),
            'create' => Pages\CreateBedroomType::route('/create'),
            'edit' => Pages\EditBedroomType::route('/{record}/edit'),
        ];
    }
}
