<?php

namespace App\Models;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\Filters\FilterListType;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Property extends Model
{
    use HasFactory, HasSlug, HasTranslations, SoftDeletes;

    public $translatable = ['title', 'description', 'address'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'township_id' => 'integer',
        'title' => 'array',
        'description' => 'array',
        'images' => 'array',
        'address' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'posted_at' => 'datetime',
        'sold_at' => 'datetime',
        'rent_at' => 'datetime',
        'completed_at' => 'datetime',
        'type' => PropertyType::class,
        'status' => PropertyStatus::class,
        'area_type' => AreaType::class,
        'area_unit' => AreaUnit::class,
        'bathrooms_count' => 'integer',
        'is_rentable' => 'boolean',
        'rent_price_type' => PropertyPriceType::class,
        'rent_price_from' => 'integer',
        'rented_price' => 'integer',
        'rent_price_to' => 'integer',
        'rent_negotiable' => 'boolean',
        'rent_owner_commission' => 'float',
        'rent_customer_commission' => 'float',
        'is_saleable' => 'boolean',
        'sold_price' => 'integer',
        'sale_price_type' => PropertyPriceType::class,
        'sale_price_from' => 'integer',
        'sale_price_to' => 'integer',
        'sale_negotiable' => 'boolean',
        'sale_owner_commission' => 'float',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Scopes
     */
    public function scopePosted(Builder $query)
    {
        $query->where('status', PropertyStatus::Posted->value);
    }

    // Scope for list type filtering (Newest, ForSale, ForRent)
    public function scopeFilterListType(Builder $query, $listType)
    {
        if ($listType) {
            $filterListType = FilterListType::from($listType);

            if ($filterListType == FilterListType::Newest) {
                $query->orderBy('posted_at', 'desc');
            } elseif ($filterListType == FilterListType::ForSale) {
                $query->where('is_saleable', true);
            } elseif ($filterListType == FilterListType::ForRent) {
                $query->where('is_rentable', true);
            }
        }
    }

    // Scope for search functionality
    public function scopeSearch(Builder $query, $search)
    {
        if ($search) {
            $keywords = explode(' ', $search);

            $query->where(function (Builder $q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                }
            });
        }
    }

    // Scope for filtering by price range
    public function scopeFilterPrice(Builder $query, $priceFrom, $priceTo)
    {
        if ($priceFrom) {
            $query->where('rent_price_from', '>=', $priceFrom);
            $query->where('sale_price_from', '>=', $priceFrom);
        }
        if ($priceTo) {
            $query->where('rent_price_to', '<=', $priceTo);
            $query->where('sale_price_to', '<=', $priceTo);
        }
    }

    // Scope for filtering by state
    public function scopeFilterState(Builder $query, $state, $township = null)
    {
        if ($state && ! $township) {
            $query->whereHas('township', fn (Builder $q) => $q->whereRelation('state', 'slug', $state));
        }
    }

    // Scope for filtering by type
    public function scopeFilterType(Builder $query, $type)
    {
        if ($type) {
            $query->where('type', $type);
        }
    }

    // Scope for filtering by township
    public function scopeFilterTownship(Builder $query, $township)
    {
        if ($township) {
            $query->whereRelation('township', 'slug', $township);
        }
    }

    /**
     * Relationships
     */
    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rateable::class, 'rateable');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(Viewable::class, 'viewable');
    }

    public function bedroomTypes(): BelongsToMany
    {
        return $this->belongsToMany(BedroomType::class, 'property_bedroom_types')
            ->using(PropertyBedroomType::class)
            ->as('property_bedroom_type')
            ->withPivot('id', 'quantity')
            ->withTimestamps();
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_properties');
    }

    public function groups(): MorphToMany
    {
        return $this->morphToMany(Group::class, 'groupable');
    }

    public function projects(): MorphToMany
    {
        return $this->morphToMany(Project::class, 'projectable');
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }

    /**
     * Accessors
     */
    protected function salePrice(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                if ($attributes['sale_price_type'] == PropertyPriceType::Fix->value) {
                    return number_format_price($attributes['sale_price_from']);
                }

                if ($attributes['sale_price_type'] == PropertyPriceType::Range->value) {
                    return number_format_price($attributes['sale_price_from']).' - '.number_format_price($attributes['sale_price_to']);
                }

                return '';
            },
        );
    }

    protected function rentPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                if ($attributes['rent_price_type'] == PropertyPriceType::Fix->value) {
                    return number_format_price($attributes['rent_price_from']);
                }

                if ($attributes['rent_price_type'] == PropertyPriceType::Range->value) {
                    return number_format_price($attributes['rent_price_from']).' - '.number_format_price($attributes['rent_price_to']);
                }

                return '';
            },
        );
    }

    protected function priceDetail(): Attribute
    {
        return Attribute::make(
            get: function () {
                $detail = [];

                if ($this->is_saleable) {
                    $acquisitionType = PropertyAcquisitionType::Sale;

                    $detail[str($acquisitionType->value)->camel()->toString()] = "{$this->sale_price} ({$acquisitionType->getLabel()})";
                }

                if ($this->is_rentable) {
                    $acquisitionType = PropertyAcquisitionType::Rent;

                    $detail[str($acquisitionType->value)->camel()->toString()] = "{$this->rent_price} ({$acquisitionType->getLabel()})";
                }

                return $detail;
            },
        );
    }

    protected function saleCommissionDescription(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->getCommissionDescription(PropertyAcquisitionType::Sale, __('Owner'), $this->sale_price_type, $this->sale_price_from, $this->sale_owner_commission);
            },
        );
    }

    protected function rentCommissionDescription(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $ownerDescription = $this->getCommissionDescription(PropertyAcquisitionType::Rent, __('Owner'), $this->rent_price_type, $this->rent_price_from, $this->rent_owner_commission);
                $customerDescription = $this->getCommissionDescription(PropertyAcquisitionType::Rent, __('Customer'), $this->rent_price_type, $this->rent_price_from, $this->rent_customer_commission);

                return $ownerDescription.', '.$customerDescription;
            },
        );
    }

    public static function getCommissionDescription(PropertyAcquisitionType $acquisitionType, string $by, PropertyPriceType $priceType, float $priceFrom, ?float $commission)
    {
        if (! $commission) {
            return '';
        }

        $commissionDescription = '';
        $totalDescription = '';

        if ($acquisitionType == PropertyAcquisitionType::Sale) {
            $commissionDescription = number_format_tran($commission).'%';

        } else {
            $tranKey = 'by_month';
            if (in_array($commission, [50, 100, 200, 300])) {
                $tranKey = 'by_month_'.(int) ($commission);
            }

            $commissionDescription = __("property.acquisition.commission.{$tranKey}", [
                'percentage' => number_format_tran($commission).'%',
            ]);
        }

        if ($priceType == PropertyPriceType::Fix && $priceFrom > 0) {
            $total = number_format($priceFrom * $commission / 100);
            $totalDescription = " ({$total})";
        }

        return __('property.acquisition.commission.by', [
            'commission' => $commissionDescription,
            'commission_by' => $by,
        ]).$totalDescription;
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn () => collect($this->images)->map(fn ($item) => is_valid_url($item) ? $item : Storage::disk('public')->url($item))->toArray()
        );
    }

    protected function squareFeet(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value,
            set: function () {
                if ($this->area_type === AreaType::LengthWidth) {
                    return $this->length * $this->width;
                }

                if ($this->area_unit === AreaUnit::Acre) {
                    return $this->area * 43560;
                }

                return $this->area;
            }
        );
    }

    protected function coverImage(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => is_valid_url($value) ? $value : Storage::disk('public')->url($value)
        );
    }

    protected function areaDescription(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->area_type === AreaType::LengthWidth) {
                    return number_format_tran($this->length).' x '.number_format_tran($this->width).' '.__('sqft');
                }

                return number_format_tran($this->area).' '.$this->area_unit->getLabel();
            },
        );
    }
}
