<?php

namespace App\Filament\Resources;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\PropertyResource\Concerns\PropertyForm;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Property;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class LeadResource extends Resource
{
    use PropertyForm;

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Leads');
    }

    public static function getModelLabel(): string
    {
        return __('Lead');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('General'))
                            ->schema([
                                Forms\Components\Select::make('interest')
                                    ->options(LeadInterest::class)
                                    ->required()
                                    ->live(),
                                Forms\Components\Toggle::make('is_owner')
                                    ->default(true)
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => $get('interest') ? LeadInterest::from($get('interest')) == LeadInterest::Renting : false)
                                    ->inline(false),
                                Forms\Components\Select::make('property_type')
                                    ->options(PropertyType::class)
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnStart(1),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->rule([
                                        'required_without:phone',
                                    ])
                                    ->columnStart(1),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('remark')
                                    ->label(__('Remark'))
                                    ->maxLength(1000)
                                    ->rules([
                                        'string',
                                        'max:1000',
                                    ])
                                    ->columnStart(1),
                                Forms\Components\Toggle::make('send_updates')
                                    ->default(true)
                                    ->required()
                                    ->inline(false),
                            ])->columns(3),
                        Forms\Components\Tabs\Tab::make(__('Address'))
                            ->schema([
                                Forms\Components\Select::make('township_id')
                                    ->relationship('township', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Textarea::make('address')
                                    ->rules([
                                        'string',
                                        'max:1000',
                                    ]),
                                Forms\Components\Select::make('preferred_contact_method')
                                    ->options(LeadContactMethod::class),
                                Forms\Components\Select::make('preferred_contact_time')
                                    ->options(LeadContactTime::class),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make(__('Property detail'))
                            ->schema([
                                Forms\Components\TextInput::make('max_price')
                                    ->numeric()
                                    ->rules([
                                        'integer',
                                        'min:100',
                                        'max:4000000000',
                                    ]),
                                Forms\Components\TextInput::make('square_feet')
                                    ->numeric()
                                    ->rules([
                                        'integer',
                                        'min:50',
                                        'max:16777215',
                                    ]),
                                Forms\Components\TextInput::make('bedrooms')
                                    ->numeric()
                                    ->rules([
                                        'integer',
                                        'min:0',
                                        'max:255',
                                    ]),
                                Forms\Components\TextInput::make('bathrooms')
                                    ->numeric()
                                    ->rules([
                                        'integer',
                                        'min:0',
                                        'max:255',
                                    ]),
                            ])->columns(4),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $authUser = Filament::auth()->user();

        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('updated_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('property_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\Action::make('create_property')
                    ->label(__('Property'))
                    ->successNotification(Notification::make()->title(__('lead.notification.property_created.title'))->success())
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

                        DB::transaction(function () use ($data, $record) {
                            $property = Property::create([
                                'owner_id' => $record->id,
                                ...$data,
                            ]);

                            $record->update([
                                'property_id' => $property->refresh()->id,
                                'status' => LeadStatus::Converted,
                            ]);
                        });

                        $action->success();
                    }),
                Tables\Actions\Action::make('contacted_lead')
                    ->label(__('Contact'))
                    ->iconButton()
                    ->modalSubmitActionLabel(__('Contacted'))
                    ->successNotification(Notification::make()->title(__('lead.notification.contacted.title'))->success())
                    ->visible(fn (Lead $record) => $authUser instanceof Admin && $authUser->can('contacted', $record))
                    ->icon('gmdi-phone')
                    ->button()
                    ->color('gray')
                    ->form(fn (Lead $lead) => [
                        Forms\Components\Placeholder::make('first_name')
                            ->content(fn () => $lead->first_name),
                        Forms\Components\Placeholder::make('last_name')
                            ->content(fn () => $lead->last_name),
                        Forms\Components\Placeholder::make('email')
                            ->content(fn () => $lead->email),
                        Forms\Components\Placeholder::make('phone')
                            ->content(fn () => $lead->phone),
                        Forms\Components\Placeholder::make('address')
                            ->content(fn () => $lead->address),
                        Forms\Components\Textarea::make('remark')
                            ->label(__('Remark'))
                            ->rules([
                                'string',
                                'max:1000',
                            ]),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $record->update([
                            'status' => LeadStatus::Contacted,
                            'remark' => $data['remark'],
                        ]);

                        $action->success();
                    }),
                Tables\Actions\Action::make('scheduled_lead')
                    ->label(__('Scheduled'))
                    ->iconButton()
                    ->successNotification(Notification::make()->title(__('lead.notification.scheduled.title'))->success())
                    ->visible(fn (Lead $record) => $authUser instanceof Admin && $authUser->can('scheduled', $record))
                    ->icon('gmdi-calendar-month-o')
                    ->button()
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('remark')
                            ->label(__('Remark'))
                            ->rules([
                                'string',
                                'max:1000',
                            ]),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $record->update([
                            'status' => LeadStatus::Scheduled,
                            'remark' => $data['remark'],
                        ]);

                        $action->success();
                    }),
                Tables\Actions\Action::make('close_lead')
                    ->label(__('Close'))
                    ->modalSubmitActionLabel(__('Confirm'))
                    ->iconButton()
                    ->successNotification(Notification::make()->title(__('lead.notification.closed.title'))->success())
                    ->visible(fn (Lead $record) => $authUser instanceof Admin && $authUser->can('close', $record))
                    ->icon('gmdi-close-o')
                    ->button()
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('remark')
                            ->label(__('Remark'))
                            ->rules([
                                'string',
                                'max:1000',
                            ]),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $record->update([
                            'status' => LeadStatus::Closed,
                            'remark' => $data['remark'],
                        ]);

                        $action->success();
                    }),
                Tables\Actions\ViewAction::make()
                    ->button(),
                // Tables\Actions\EditAction::make()
                //     ->button()
                //     ->color('warning'),
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
