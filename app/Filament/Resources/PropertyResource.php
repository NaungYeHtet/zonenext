<?php

namespace App\Filament\Resources;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\Township;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Property management');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('General')
                            ->schema([
                                Infolists\Components\TextEntry::make('title'),
                                Infolists\Components\TextEntry::make('type')
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
                            ]),
                        Infolists\Components\Tabs\Tab::make('Price')
                            ->schema([
                                Infolists\Components\Fieldset::make('Sell')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('sell_price')
                                            ->label(__('Price')),
                                        Infolists\Components\IconEntry::make('sell_negotiable')
                                            ->label(__('Negotiable'))
                                            ->boolean(),
                                        Infolists\Components\TextEntry::make('sell_commission_description')
                                            ->label(__('Commission')),
                                    ])
                                    ->visible(fn (Model $record) => $record->is_sellable),
                                Infolists\Components\Fieldset::make('Rent')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('rent_price')
                                            ->label(__('Price')),
                                        Infolists\Components\IconEntry::make('rent_negotiable')
                                            ->label(__('Negotiable'))
                                            ->boolean(),
                                        Infolists\Components\TextEntry::make('rent_commission_description')
                                            ->label(__('Commission')),
                                    ])
                                    ->visible(fn (Model $record) => $record->is_rentable),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Gallery')
                            ->schema([
                                Infolists\Components\ImageEntry::make('cover_image_url')
                                    ->label(__('Cover image')),
                                Infolists\Components\ImageEntry::make('gallery')
                                    ->label(__('Images'))
                                    ->height(100),
                            ]),
                    ])->columns(2),
            ])->columns(1);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    self::getGeneralFormStep(),
                    self::getAddressFormStep(),
                    self::getPriceFormStep(),
                    self::getAreaFormStep(),
                    self::getGalleryFormStep(),
                ])
                    ->skippable()
                    ->startOnStep(5)
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
    <x-filament::button
        type="submit"
        size="sm"
    >
        {{__('filament-panels::resources/pages/create-record.form.actions.create.label')}}
    </x-filament::button>
BLADE))),
            ])
            ->columns(1);
    }

    public static function getGeneralFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('General')
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(PropertyType::class)
                    ->default(PropertyType::Apartment),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->default(fake()->sentence)
                    ->rules([
                        'required',
                        'string',
                    ])
                    ->translatable()
                    ->columnSpan(2),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->default(fake()->paragraph)
                    ->rules([
                        'required',
                        'max:1000',
                    ])
                    ->translatable()
                    ->columnStart(1)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function getGalleryFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Gallery')
            ->schema([
                Forms\Components\FileUpload::make('cover_image')
                    ->required()
                    ->disk('public')
                    ->directory('property')
                    ->image()
                    ->rules([
                        'required',
                    ])
                    ->maxSize('5000')
                    ->openable(),
                Forms\Components\FileUpload::make('images')
                    ->label(__('Images'))
                    ->disk('public')
                    ->directory('property')
                    ->multiple()
                    ->image()
                    ->maxFiles(10)
                    ->openable(),
            ])
            ->columns(3);
    }

    public static function getAddressFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Address')
            ->schema([
                Forms\Components\Select::make('township_id')
                    ->label(__('Township'))
                    ->relationship('township', 'name')
                    ->default(Township::where('state_id', 13)->inRandomOrder()->first()->id)
                    ->rules([
                        'required',
                    ])
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('address')
                    ->required()
                    ->default(fake()->address)
                    ->rules([
                        'required',
                        'max:1000',
                    ])
                    ->translatable()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->default(16.7983776)
                    ->rules([
                        'required',
                        'numeric',
                        'between:-90,90',
                    ]),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->default(96.1469824)
                    ->rules([
                        'required',
                        'numeric',
                        'between:-180,180',
                    ]),
            ])
            ->columns(3);
    }

    public static function getAreaFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Area')
            ->schema([
                Forms\Components\Select::make('area_type')
                    ->label(__('Type'))
                    ->options(AreaType::class)
                    ->default(AreaType::LengthWidth->value)
                    ->required()
                    ->rules([
                        'required',
                    ])
                    ->live(),
                Forms\Components\Section::make(__('Length width'))
                    ->schema([
                        Forms\Components\TextInput::make('length')
                            ->required()
                            ->numeric()
                            ->default(fake()->randomNumber(2))
                            ->rules([
                                'required',
                                'numeric',
                                'min:1',
                            ]),
                        Forms\Components\TextInput::make('width')
                            ->required()
                            ->numeric()
                            ->default(fake()->randomNumber(2))
                            ->rules([
                                'required',
                                'numeric',
                                'min:1',
                            ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('area_type') === AreaType::LengthWidth->value)
                    ->columns(2),
                Forms\Components\Section::make(__('Area'))
                    ->schema([
                        Forms\Components\Select::make('area_unit')
                            ->label(__('Unit'))
                            ->options(AreaUnit::class)
                            ->required()
                            ->rules([
                                'required',
                            ]),
                        Forms\Components\TextInput::make('area')
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'numeric',
                                'min:1',
                            ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('area_type') === AreaType::Area->value)
                    ->columns(2),
            ])
            ->columns(3);
    }

    public static function getPriceFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Price')
            ->schema([
                Forms\Components\Toggle::make('is_sellable')
                    ->label(__('Sell'))
                    ->rules([
                        'required_if:rentable,false',
                        'boolean',
                    ])
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => ! $state ? $set('is_rentable', true) : ''),
                Forms\Components\Section::make(__('Sell'))
                    ->schema([
                        Forms\Components\Select::make('sell_price_type')
                            ->label(__('Type'))
                            ->options(PropertyPriceType::class)
                            ->default(PropertyPriceType::Fix->value)
                            ->required()
                            ->rules([
                                'required',
                            ])
                            ->live(),
                        Forms\Components\TextInput::make('sell_price_from')
                            ->label(fn (Forms\Get $get) => $get('sell_price_type') === PropertyPriceType::Range->value ? __('From') : __('Price'))
                            ->default(get_stepped_random_number(60000000, 600000000 / 2, 5000000))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->live(true),
                        Forms\Components\TextInput::make('sell_price_to')
                            ->label(__('To'))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('sell_price_type') === PropertyPriceType::Range->value),
                        Forms\Components\Toggle::make('sell_negotiable')
                            ->label(__('Negotiable'))
                            ->required()
                            ->rules([
                                'required',
                                'boolean',
                            ])
                            ->columnStart(1),
                        Forms\Components\TextInput::make('sell_owner_commission')
                            ->label(__('Commission').' ('.__('Owner').')')
                            ->required()
                            ->default(1)
                            ->numeric()
                            ->rules([
                                'required',
                                'numeric',
                                'min:1',
                                'max:50',
                            ])
                            ->live(true),
                        Forms\Components\Placeholder::make('commission_detail')
                            ->label('')
                            ->content(function (Forms\Get $get): string {
                                $ownerCommission = (float) $get('sell_owner_commission') ?? 0;
                                $priceType = PropertyPriceType::from($get('sell_price_type'));
                                $price = (float) $get('sell_price_from') ?? 0;

                                $ownerDescription = Property::getCommissionDescription(PropertyAcquisitionType::Sell, __('Owner'), $priceType, $price, $ownerCommission);

                                return $ownerDescription;
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('is_sellable'))
                    ->columns(3),
                Forms\Components\Toggle::make('is_rentable')
                    ->label(__('Rent'))
                    ->required()
                    ->rules([
                        'required_if:is_sellable,false',
                        'required',
                        'boolean',
                    ])
                    ->live()
                    ->columnStart(1)
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => ! $state ? $set('is_sellable', true) : '')
                    ->default(true),
                Forms\Components\Section::make(__('Rent'))
                    ->schema([
                        Forms\Components\Select::make('rent_price_type')
                            ->label(__('Type'))
                            ->options(PropertyPriceType::class)
                            ->default(PropertyPriceType::Fix->value)
                            ->required()
                            ->rules([
                                'required',
                            ])
                            ->live(),
                        Forms\Components\TextInput::make('rent_price_from')
                            ->label(fn (Forms\Get $get) => $get('rent_price_type') === PropertyPriceType::Range->value ? __('From') : __('Price'))
                            ->default(get_stepped_random_number(300000, 6000000 / 2, 50000))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->live(true),
                        Forms\Components\TextInput::make('rent_price_to')
                            ->label(__('To'))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('rent_price_type') === PropertyPriceType::Range->value),
                        Forms\Components\Toggle::make('rent_negotiable')
                            ->label(__('Negotiable'))
                            ->required()
                            ->rules([
                                'required',
                                'boolean',
                            ])
                            ->columnStart(1),
                        Forms\Components\TextInput::make('rent_owner_commission')
                            ->label(__('Commission').' ('.__('Owner').')')
                            ->required()
                            ->default(100)
                            ->numeric()
                            ->rules([
                                'required',
                                'numeric',
                                'min:0',
                                'max:300',
                            ])
                            ->live(true),
                        Forms\Components\TextInput::make('rent_customer_commission')
                            ->label(__('Commission').' ('.__('Customer').')')
                            ->required()
                            ->default(100)
                            ->numeric()
                            ->rules([
                                'required',
                                'numeric',
                                'min:0',
                                'max:300',
                            ])
                            ->live(true),
                        Forms\Components\Placeholder::make('commission_detail')
                            ->label('')
                            ->content(function (Forms\Get $get): string {
                                $ownerCommission = (float) $get('rent_owner_commission') ?? 0;
                                $customerCommission = (float) $get('rent_customer_commission') ?? 0;
                                $priceType = PropertyPriceType::from($get('rent_price_type'));
                                $price = (float) $get('rent_price_from') ?? 0;

                                $ownerDescription = Property::getCommissionDescription(PropertyAcquisitionType::Rent, __('Owner'), $priceType, $price, $ownerCommission);
                                $customerDescription = Property::getCommissionDescription(PropertyAcquisitionType::Rent, __('Customer'), $priceType, $price, $customerCommission);

                                return $ownerDescription.', '.$customerDescription;
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('is_rentable'))
                    ->columns(2),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label(__('Cover image')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
