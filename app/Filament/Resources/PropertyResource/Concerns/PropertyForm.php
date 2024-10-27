<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\Township;
use Filament\Forms;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

trait PropertyForm
{
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Wizard::make([
                self::getGeneralFormStep(),
                self::getAddressFormStep(),
                self::getPriceFormStep(),
                self::getAreaFormStep(),
                self::getGalleryFormStep(),
            ])
                ->skippable()
                ->startOnStep(5)
                ->submitAction(self::getSubmitAction()),
        ];
    }

    public static function getGeneralFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('General')
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(PropertyType::class)
                    ->default(PropertyType::Apartment)
                    ->required(),
                Forms\Components\Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),
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
                Forms\Components\Toggle::make('is_saleable')
                    ->label(__('Sale'))
                    ->rules([
                        'required_if:rentable,false',
                        'boolean',
                    ])
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => ! $state ? $set('is_rentable', true) : ''),
                Forms\Components\Section::make(__('Sale'))
                    ->schema([
                        Forms\Components\Select::make('sale_price_type')
                            ->label(__('Type'))
                            ->options(PropertyPriceType::class)
                            ->default(PropertyPriceType::Fix->value)
                            ->required()
                            ->rules([
                                'required',
                            ])
                            ->live(),
                        Forms\Components\TextInput::make('sale_price_from')
                            ->label(fn (Forms\Get $get) => $get('sale_price_type') === PropertyPriceType::Range->value ? __('From') : __('Price'))
                            ->default(get_stepped_random_number(60000000, 600000000 / 2, 5000000))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->live(true),
                        Forms\Components\TextInput::make('sale_price_to')
                            ->label(__('To'))
                            ->required()
                            ->numeric()
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('sale_price_type') === PropertyPriceType::Range->value),
                        Forms\Components\Toggle::make('sale_negotiable')
                            ->label(__('Negotiable'))
                            ->required()
                            ->rules([
                                'required',
                                'boolean',
                            ])
                            ->columnStart(1),
                        Forms\Components\TextInput::make('seller_commission')
                            ->label(__('Commission').' ('.__('Seller').')')
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
                                $sellerCommission = (float) $get('seller_commission') ?? 0;
                                $priceType = PropertyPriceType::from($get('sale_price_type'));
                                $price = (float) $get('sale_price_from') ?? 0;

                                return Property::getCommissionDescription(PropertyAcquisitionType::Sale, __('Seller'), $priceType, $price, $sellerCommission);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('is_saleable'))
                    ->columns(3),
                Forms\Components\Toggle::make('is_rentable')
                    ->label(__('Rent'))
                    ->required()
                    ->rules([
                        'required_if:is_saleable,false',
                        'required',
                        'boolean',
                    ])
                    ->live()
                    ->columnStart(1)
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => ! $state ? $set('is_saleable', true) : '')
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
                        Forms\Components\TextInput::make('landlord_commission')
                            ->label(__('Commission').' ('.__('Landlord').')')
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
                        Forms\Components\TextInput::make('renter_commission')
                            ->label(__('Commission').' ('.__('Renter').')')
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
                                $landlordCommission = (float) $get('landlord_commission') ?? 0;
                                $renterCommission = (float) $get('renter_commission') ?? 0;
                                $priceType = PropertyPriceType::from($get('rent_price_type'));
                                $price = (float) $get('rent_price_from') ?? 0;

                                $landlordCommission = Property::getCommissionDescription(PropertyAcquisitionType::Rent, __('Landlord'), $priceType, $price, $landlordCommission);
                                $renterDescription = Property::getCommissionDescription(PropertyAcquisitionType::Rent, __('Renter'), $priceType, $price, $renterCommission);

                                return $landlordCommission.', '.$renterDescription;
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('is_rentable'))
                    ->columns(2),
            ])
            ->columns(3);
    }

    public static function getSubmitAction(): HtmlString
    {
        return new HtmlString(Blade::render(<<<'BLADE'
        <x-filament::button
            type="submit"
            size="sm"
        >
            {{__('filament-panels::resources/pages/create-record.form.actions.create.label')}}
        </x-filament::button>
    BLADE));
    }
}
