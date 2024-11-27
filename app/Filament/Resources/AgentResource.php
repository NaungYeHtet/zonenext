<?php

namespace App\Filament\Resources;

use App\Enums\Lead\LeadType;
use App\Enums\PropertyType;
use App\Filament\Resources\AgentResource\Pages;
use App\Models\Admin;
use App\Models\Township;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;

class AgentResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Agents');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Account management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('avatars/admins')
                    ->maxSize(2000)
                    ->avatar()
                    ->imageEditor()
                    ->circleCropper(),
                Forms\Components\TextInput::make('name')
                    ->default(fake()->name())
                    ->required()
                    ->maxLength(255)
                    ->columnStart(1),
                Forms\Components\TextInput::make('email')
                    ->default(fake()->email())
                    ->autocomplete('new-email')
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->default(fake()->e164PhoneNumber())
                    ->tel()
                    ->autocomplete('new-phone')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->hidden(fn(string $operation) => $operation === 'edit')
                    ->autocomplete('new-password')
                    ->password()
                    ->dehydrateStateUsing(fn(string $state): string => bcrypt($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->revealable()
                    ->rules([
                        Password::defaults(),
                    ])
                    ->required(),
                Forms\Components\Select::make('preferred_lead_types')
                    ->options(LeadType::class)
                    ->multiple(),
                Forms\Components\Select::make('preferred_property_types')
                    ->options(PropertyType::class)
                    ->multiple(),
                Forms\Components\Select::make('preferred_townships')
                    ->options(Township::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('created_at', 'desc')->agent())
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(asset('images/avatars/admin.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
        ];
    }
}
