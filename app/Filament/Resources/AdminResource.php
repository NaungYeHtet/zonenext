<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'gmdi-admin-panel-settings-o';

    public static function getModelLabel(): string
    {
        return __('Admins');
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
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('name', '!=', 'Agent'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->default(fake()->name())
                    ->required()
                    ->maxLength(255),
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
                    ->hidden(fn (string $operation) => $operation === 'edit')
                    ->autocomplete('new-password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => bcrypt($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->revealable()
                    ->rules([
                        Password::defaults(),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('roles', 'name', '!=', 'Agent'))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(asset('images/avatars/admin.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Role'))
                    ->badge()
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
