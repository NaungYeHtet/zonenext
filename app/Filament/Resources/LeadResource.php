<?php

namespace App\Filament\Resources;

use App\Enums\PropertyStatus;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\PropertyResource\Concerns\PropertyForm;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Property;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    use PropertyForm;

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Leads');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
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
        $authUser = Filament::auth()->user();

        return $table
            ->modifyQueryUsing(function ($query) use ($authUser) {
                if ($authUser instanceof Admin && $authUser->hasRole('Agent')) {
                    $query->where('admin_id', $authUser->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('interest')
                    ->badge(),
                Tables\Columns\TextColumn::make('property_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('create_property')
                    ->label(__('Property'))
                    ->model(Property::class)
                    ->successNotification(Notification::make()->title(__('Property created successfully'))->success())
                    ->visible(fn (Lead $record) => $authUser instanceof Admin && $authUser->can('createProperty', $record))
                    ->icon('gmdi-add')
                    ->button()
                    ->color('success')
                    ->form(fn (Lead $record) => static::getPropertyForm($record))
                    ->modalSubmitAction(false)
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $data['status'] = PropertyStatus::Draft;
                        $data['square_feet'] = null;

                        Property::create([
                            'owner_id' => $record->id,
                            ...$data,
                        ]);

                        $action->success();
                    }),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
