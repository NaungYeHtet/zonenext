<?php

namespace App\Filament\Resources\PropertyResource\Concerns;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\Lead\LeadInterest;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Township;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

trait PropertyForm
{
    public static function getPropertyForm(?Lead $lead = null): array
    {
        return [
            Forms\Components\Wizard::make([
                self::getGeneralFormStep($lead),
                self::getAddressFormStep(),
                self::getPriceFormStep($lead),
                self::getAreaFormStep(),
                self::getGalleryFormStep(),
            ])
                // ->skippable()
                ->startOnStep(1)
                ->submitAction(self::getSubmitAction())
                ->columns(1)
                ->columnSpanFull(),
        ];
    }

    public static function getGeneralFormStep(?Lead $lead): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('General')
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])->schema([
                    Forms\Components\Placeholder::make('lead_name')
                        ->label(__('Lead'))
                        ->content(fn(?Model $record) => $lead ? $lead->name : $record->owner->name),
                    Forms\Components\Select::make('type')
                        ->options(PropertyType::class)
                        ->default(fn() => $lead?->property_type?->value)
                        ->required(),
                    Forms\Components\Select::make('tags')
                        ->multiple()
                        ->model(Property::class)
                        ->relationship('tags', 'name')
                        ->preload()
                        ->searchable()
                        ->maxItems(5)
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->default(fake()->sentence)
                        ->rules([
                            'required',
                            'string',
                        ])
                        ->translatable()
                        ->columnSpanFull(),
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
                ]),
            ])
            ->columnSpanFull();
    }

    public static function getAddressFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Address')
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])->schema([
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
                        ->columnSpanFull(),
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
                ]),
            ])
            ->columnSpanFull()
            ->columns(1);
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
                    ->live()
                    ->selectablePlaceholder(false),
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
                                'integer'
                            ]),
                        Forms\Components\TextInput::make('width')
                            ->required()
                            ->numeric()
                            ->default(fake()->randomNumber(2))
                            ->rules([
                                'required',
                                'numeric',
                                'min:1',
                                'integer'
                            ]),
                    ])
                    ->visible(fn(Forms\Get $get) => $get('area_type') === AreaType::LengthWidth->value)
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
                                'integer'
                            ]),
                    ])
                    ->visible(fn(Forms\Get $get) => $get('area_type') === AreaType::Area->value)
                    ->columns(2),
            ])
            ->columns(3);
    }

    public static function getPriceFormStep(?Lead $lead): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Price')
            ->schema([
                Forms\Components\Hidden::make('acquisition_type')
                    ->default(fn() => $lead?->interest === LeadInterest::Selling ? PropertyAcquisitionType::Sale->value : PropertyAcquisitionType::Rent->value)
                    ->required()
                    ->rules([
                        'required',
                    ])
                    ->live(),
                Forms\Components\Select::make('price_type')
                    ->label(__('Type'))
                    ->options(PropertyPriceType::class)
                    ->default(PropertyPriceType::Fix->value)
                    ->required()
                    ->rules([
                        'required',
                    ])
                    ->live()
                    ->selectablePlaceholder(false),
                Forms\Components\TextInput::make('price_from')
                    ->label(fn(Forms\Get $get) => $get('price_type') === PropertyPriceType::Range->value ? __('From') : __('Price'))
                    ->default(get_stepped_random_number(60000000, 600000000 / 2, 5000000))
                    ->required()
                    ->numeric()
                    ->rules([
                        'required',
                        'integer',
                        'min:1',
                    ])
                    ->live(true),
                Forms\Components\TextInput::make('price_to')
                    ->label(__('To'))
                    ->required()
                    ->numeric()
                    ->rules([
                        'required',
                        'integer',
                        'min:1',
                    ])
                    ->visible(fn(Forms\Get $get) => $get('price_type') === PropertyPriceType::Range->value),
                Forms\Components\Toggle::make('negotiable')
                    ->required()
                    ->rules([
                        'required',
                        'boolean',
                    ])
                    ->columnStart(1),
                Forms\Components\TextInput::make('owner_commission')
                    ->label(__('Commission') . ' (' . __('Owner') . ')')
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
                Forms\Components\TextInput::make('customer_commission')
                    ->label(__('Commission') . ' (' . __('Renter') . ')')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->rules([
                        'required',
                        'numeric',
                        'min:1',
                        'max:50',
                    ])
                    ->live(true)
                    ->visible(fn(Forms\Get $get) => $get('acquisition_type') === PropertyAcquisitionType::Rent->value),
                Forms\Components\Placeholder::make('commission_detail')
                    ->label('')
                    ->content(function (Forms\Get $get): string {
                        $content = '';
                        $ownerCommission = (float) $get('owner_commission') ?? 0;
                        $priceType = PropertyPriceType::from($get('price_type'));
                        $price = (float) $get('price_from') ?? 0;
                        $acquisitionType = PropertyAcquisitionType::from($get('acquisition_type'));
                        $ownerLabel = __('Owner');

                        $content .= Property::getCommissionDescription($acquisitionType, $ownerLabel, $priceType, $price, $ownerCommission);

                        if ($acquisitionType == PropertyAcquisitionType::Rent) {
                            $customerCommission = (float) $get('customer_commission') ?? 0;
                            $customerCommissionText = Property::getCommissionDescription($acquisitionType, __('Renter'), $priceType, $price, $customerCommission);
                            $ownerLabel = __('Landlord');
                            $content .= ", {$customerCommissionText}";
                        }

                        return $content;
                    })
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function getGalleryFormStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Gallery')
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])->schema([
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
                ]),
            ])
            ->columns(1);
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
