<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\LeadStatus;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\PropertyResource\Concerns\PropertyForm;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Property;
use App\Notifications\PropertyCreatedNotification;
use App\Notifications\PropertyPurchasedNotification;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeadResource extends Resource
{
    use PropertyForm;

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $isDiscovered = false;

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

    public static function getNavigationBadgeQuery(): Builder
    {
        $authUser = Filament::auth()->user();
        if (! $authUser instanceof Admin) {
            abort(403);
        }

        $leadStatus = $authUser->hasRole('Agent') ? LeadStatus::Assigned : LeadStatus::New;

        return static::getModel()::where('status', $leadStatus->value);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('General'))
                            ->schema([
                                Forms\Components\Select::make('property_type')
                                    ->options(PropertyType::class)
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->rule([
                                        'required_without:phone',
                                    ]),
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
            ->modifyQueryUsing(fn($query) => $query->orderBy('updated_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('property.code')
                    ->description(function (Lead $record) {
                        return $record->property?->status->getLabel();
                    })
                    ->copyable()
                    ->copyMessage('Copied to clipboard')
                    ->searchable(),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label(__('Agent'))
                    ->searchable()
                    ->hidden($authUser instanceof Admin && $authUser->hasRole('Agent')),
                Tables\Columns\TextColumn::make('property_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LeadStatus::class)
                    ->multiple()
                    ->native(false),
                Tables\Filters\SelectFilter::make('agent')
                    ->label(__('Agent'))
                    ->relationship('admin', 'name')
                    ->multiple()
                    ->native(false)
                    ->preload()
                    ->visible(fn() => $authUser instanceof Admin && ! $authUser->hasRole('Agent')),
                Tables\Filters\SelectFilter::make('property_code')
                    ->relationship('property', 'code')
                    ->multiple()
                    ->native(false)
                    ->preload(),
            ], FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('assign_agent')
                    ->label(__('Assign'))
                    ->icon('gmdi-person-add')
                    ->button()
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('assignAgent', $record))
                    ->form(fn(Lead $record) => [
                        Forms\Components\Select::make('admin_id')
                            ->label(__('Agent'))
                            ->options(Admin::agent()->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->modalSubmitActionLabel(__('Assign'))
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $admin = \App\Models\Admin::find($data['admin_id']);
                        $record->update([
                            'status' => LeadStatus::Assigned,
                            'admin_id' => $admin->id,
                        ]);

                        $admin->notify(new \App\Notifications\LeadAssignedNotification($record));

                        Notification::make()
                            ->title(__('lead_trans.notification.agent_assigned.title', ['agent' => $admin->name]))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('create_property')
                    ->label(__('Property'))
                    ->successNotification(Notification::make()->title(__('lead_trans.notification.property_created.title'))->success())
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('createProperty', $record))
                    ->icon('gmdi-add')
                    ->button()
                    ->color('success')
                    ->form(fn(Lead $record) => static::getPropertyForm($record))
                    ->modalSubmitAction(false)
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        DB::transaction(function () use ($data, $record) {
                            $property = Property::create([
                                'status' => PropertyStatus::Posted,
                                'square_feet' => null,
                                'owner_id' => $record->id,
                                ...$data,
                                'posted_at' => now()
                            ]);

                            $record->update([
                                'property_id' => $property->refresh()->id,
                                'status' => LeadStatus::Converted,
                            ]);

                            $record->notify(new PropertyCreatedNotification($property));
                        });

                        $action->success();
                    }),
                Tables\Actions\Action::make('purchase')
                    ->label(fn(Lead $record) => $record->interest->getPropertyAcquisitionType() == PropertyAcquisitionType::Sale ? __('property_trans.actions.sold.label') : __('property_trans.actions.rent.label'))
                    ->successNotification(Notification::make()->title(__('lead_trans.notification.purchased.title'))->success())
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('purchaseProperty', $record))
                    ->icon('gmdi-check')
                    ->fillForm(fn(Lead $record) => [
                        'property_id' => $record->property ? ($record->property->status == PropertyStatus::Posted ? $record->property->id : null) : null,
                        'purchased_price' => $record->property ? $record->property->price_from : null,
                        'owner_commission' => $record->property ? $record->property->owner_commission : null,
                        'customer_commission' => $record->property ? $record->property->customer_commission : null,
                    ])
                    ->button()
                    ->color('success')
                    ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
                    ->form(fn(Lead $record) => [
                        Forms\Components\Split::make([
                            Forms\Components\Section::make([
                                Forms\Components\Placeholder::make('title')
                                    ->label(__('Title'))
                                    ->content(fn(Forms\Get $get) => Property::find($get('property_id'))?->title),
                                Forms\Components\Placeholder::make('price')
                                    ->label(__('Price'))
                                    ->content(fn(Forms\Get $get) => Property::find($get('property_id'))?->price),
                                Forms\Components\Placeholder::make('commission')
                                    ->label(__('Commission'))
                                    ->content(fn(Forms\Get $get) => Property::find($get('property_id'))?->commission_description),
                            ]),
                            Forms\Components\Section::make([
                                Forms\Components\Select::make('property_id')
                                    ->options(function (Lead $record) {
                                        return Property::where('status', PropertyStatus::Posted->value)
                                            ->where('acquisition_type', $record->interest->getPropertyAcquisitionType())->pluck('code', 'id');
                                    })
                                    ->label('Property code')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                        if ($state) {
                                            $property = Property::find($state);
                                            $set('purchased_price', $property->price_from);
                                            $set('owner_percent', $property->owner_commission);
                                            $set('customer_percent', $property->customer_commission);
                                        }
                                    })
                                    ->live(),
                                Forms\Components\TextInput::make('purchased_price')
                                    ->label(fn(Lead $record) => $record->interest->getPropertyAcquisitionType() == PropertyAcquisitionType::Sale ? __('Sold price') : __('Rented price'))
                                    ->numeric()
                                    ->required()
                                    ->rules([
                                        'required',
                                        'numeric',
                                        'integer',
                                        'min:1',
                                    ])
                                    ->live(),
                                Forms\Components\TextInput::make('owner_commission')
                                    ->label(fn(Lead $record) => $record->interest->getPropertyAcquisitionType() == PropertyAcquisitionType::Sale ? __('Commission (Seller)') : __('Commission (Landlord)'))
                                    ->numeric()
                                    ->required()
                                    ->rules([
                                        'required',
                                        'numeric',
                                        'integer',
                                        'min:1',
                                    ])
                                    ->live(),
                                Forms\Components\TextInput::make('customer_commission')
                                    ->label(fn() => __('Commission (Renter)'))
                                    ->numeric()
                                    ->required()
                                    ->rules([
                                        'required',
                                        'numeric',
                                        'integer',
                                        'min:1',
                                    ])
                                    ->live()
                                    ->visible(fn(Forms\Get $get) => Property::find($get('property_id'))?->acquisition_type == PropertyAcquisitionType::Rent),
                                Forms\Components\Placeholder::make('purchased_commission')
                                    ->label(fn(Lead $record) => $record->interest->getPropertyAcquisitionType() == PropertyAcquisitionType::Sale ? __('Sold commission') : __('Rented commission'))
                                    ->content(function (Forms\Get $get) {
                                        $purchasedPrice = (float) $get('purchased_price') ?? 0;
                                        $ownerCommission = (float) $get('owner_commission') ?? 0;
                                        $customerCommission = (float) $get('customer_commission') ?? 0;

                                        return number_format_price(($purchasedPrice * $ownerCommission / 100) + ($purchasedPrice * $customerCommission / 100));
                                    }),
                            ]),

                        ])->from('md'),
                    ])
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        DB::transaction(function () use ($data, $record) {
                            $property = Property::find($data['property_id']);
                            $purchasedPrice = $data['purchased_price'];
                            $ownerCommission = $data['owner_commission'];
                            $customerCommission = $data['customer_commission'] ?? 0;

                            $property->update([
                                'customer_id' => $record->id,
                                'purchased_at' => now(),
                                'purchased_price' => $purchasedPrice,
                                'purchased_commission' => ($purchasedPrice * $ownerCommission / 100) + ($purchasedPrice * $customerCommission / 100),
                                'status' => PropertyStatus::Purchased,
                            ]);

                            $record->update([
                                'property_id' => $property->id,
                                'status' => LeadStatus::Converted,
                            ]);

                            $property->owner->notify(new PropertyPurchasedNotification($property));
                            $record->notify(new PropertyPurchasedNotification($property));
                        });

                        $action->success();
                    }),
                Tables\Actions\Action::make('contacted_lead')
                    ->label(__('Contact'))
                    ->iconButton()
                    ->modalSubmitActionLabel(__('Contacted'))
                    ->successNotification(Notification::make()->title(__('lead_trans.notification.contacted.title'))->success())
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('contacted', $record))
                    ->icon('gmdi-phone')
                    ->button()
                    ->color('gray')
                    ->form(fn(Lead $lead) => [
                        Forms\Components\Placeholder::make('first_name')
                            ->content(fn() => $lead->first_name),
                        Forms\Components\Placeholder::make('last_name')
                            ->content(fn() => $lead->last_name),
                        Forms\Components\Placeholder::make('email')
                            ->content(fn() => $lead->email),
                        Forms\Components\Placeholder::make('phone')
                            ->content(fn() => $lead->phone),
                        Forms\Components\Placeholder::make('address')
                            ->content(fn() => $lead->address),
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
                Tables\Actions\Action::make('appointment_lead')
                    ->label(__('Appointment'))
                    ->icon('gmdi-calendar-month-o')
                    ->iconButton()
                    ->successNotification(Notification::make()->title(__('lead_trans.notification.appointment_created.title'))->success())
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('appointment', $record))
                    ->button()
                    ->color('gray')
                    ->form([
                        Forms\Components\DateTimePicker::make('date')
                            ->label(__('Time'))
                            ->rules([
                                'required',
                                'date',
                            ]),
                        Forms\Components\Textarea::make('remark')
                            ->label(__('Remark'))
                            ->rules([
                                'string',
                                'max:1000',
                            ]),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                        $record->appointments()->create([
                            'date' => $data['date'],
                            'status' => AppointmentStatus::Pending
                        ]);

                        $record->update([
                            'remark' => $data['remark'],
                        ]);

                        $action->success();
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('edit_appointment')
                        ->label(__('Edit'))
                        ->icon('heroicon-m-pencil-square')
                        ->iconButton()
                        ->successNotification(Notification::make()->title(__('lead_trans.notification.appointment_updated.title'))->success())
                        ->button()
                        ->color('gray')
                        ->fillForm(fn(Lead $record) => [
                            'date' => $record->appointments()->active()->first()?->date,
                            'remark' => $record->remark,
                        ])
                        ->form([
                            Forms\Components\DateTimePicker::make('date')
                                ->label(__('Time'))
                                ->rules([
                                    'required',
                                    'date',
                                ]),
                            Forms\Components\Textarea::make('remark')
                                ->label(__('Remark'))
                                ->rules([
                                    'string',
                                    'max:1000',
                                ]),
                        ])
                        ->modalWidth(MaxWidth::Medium)
                        ->action(function (array $data, Lead $record, Tables\Actions\Action $action) {
                            $record->appointments()->create([
                                'date' => $data['date'],
                                'status' => AppointmentStatus::Pending
                            ]);

                            $record->update([
                                'remark' => $data['remark'],
                            ]);

                            $action->success();
                        }),
                    Tables\Actions\Action::make('cancel_appointment')
                        ->label(__('Cancel'))
                        ->icon('gmdi-close-o')
                        ->iconButton()
                        ->successNotification(Notification::make()->title(__('lead_trans.notification.appointment_cancelled.title'))->success())
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
                            $record->appointments()->create([
                                'status' => AppointmentStatus::Cancelled
                            ]);

                            $record->update([
                                'remark' => $data['remark'],
                            ]);

                            $action->success();
                        }),
                    Tables\Actions\Action::make('followed_up')
                        ->label(__('Followed up'))
                        ->icon('gmdi-close-o')
                        ->iconButton()
                        ->successNotification(Notification::make()->title(__('lead_trans.notification.appointment_followed_up.title'))->success())
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
                            $appointment = $record->appointments()->active();

                            if ($appointment) {
                                $appointment->update([
                                    'status' => AppointmentStatus::Completed
                                ]);
                            }

                            $record->update([
                                'status' => LeadStatus::FollowedUp,
                                'remark' => $data['remark'],
                            ]);

                            $action->success();
                        }),
                ])
                    ->button()
                    ->icon('heroicon-m-pencil-square')
                    ->label(__('Appointment'))
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && ($authUser->can('updateAppointment', $record))),
                Tables\Actions\Action::make('close_lead')
                    ->label(__('Close'))
                    ->modalSubmitActionLabel(__('Confirm'))
                    ->iconButton()
                    ->successNotification(Notification::make()->title(__('lead_trans.notification.closed.title'))->success())
                    ->visible(fn(Lead $record) => $authUser instanceof Admin && $authUser->can('close', $record))
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
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->recordAction(null)
            ->recordUrl(null);
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
