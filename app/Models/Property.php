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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Property extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

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
        'purchased_at' => 'datetime',
        'completed_at' => 'datetime',
        'acquisition_type' => PropertyAcquisitionType::class,
        'type' => PropertyType::class,
        'status' => PropertyStatus::class,
        'area_type' => AreaType::class,
        'area_unit' => AreaUnit::class,
        'bathrooms_count' => 'integer',
        'price_type' => PropertyPriceType::class,
        'price_from' => 'integer',
        'price_to' => 'integer',
        'negotiable' => 'boolean',
        'owner_commission' => 'float',
        'customer_commission' => 'float',
        'purchased_price' => 'integer',
        'purchased_commission' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
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
                $query->where('acquisition_type', PropertyAcquisitionType::Sale);
            } elseif ($filterListType == FilterListType::ForRent) {
                $query->where('acquisition_type', PropertyAcquisitionType::Rent);
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
            $query->where('price_from', '>=', $priceFrom);
        }
        if ($priceTo) {
            $query->where('price_to', '<=', $priceTo);
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
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function ratings(): MorphToMany
    {
        return $this->morphToMany(Rateable::class, 'rateable');
    }

    public function views(): MorphToMany
    {
        return $this->morphToMany(Viewable::class, 'viewable');
    }

    public function bedroomTypes(): BelongsToMany
    {
        return $this->belongsToMany(BedroomType::class, 'property_bedroom_types')
            ->withPivot('id', 'quantity')
            ->withTimestamps();
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'owner_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'customer_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                if ($attributes['price_type'] == PropertyPriceType::Fix->value) {
                    return number_format_price($attributes['price_from']);
                }

                if ($attributes['price_type'] == PropertyPriceType::Range->value) {
                    return number_format_price($attributes['price_from']).' - '.number_format_price($attributes['price_to']);
                }

                return '';
            },
        );
    }

    protected function commissionDescription(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->acquisition_type == PropertyAcquisitionType::Sale) {
                    return $this->getCommissionDescription(PropertyAcquisitionType::Sale, __('Seller'), $this->price_type, $this->price_from, $this->owner_commission);
                }

                $ownerDescription = $this->getCommissionDescription(PropertyAcquisitionType::Rent, __('Landloard'), $this->price_type, $this->price_from, $this->owner_commission);
                $customerDescription = $this->getCommissionDescription(PropertyAcquisitionType::Rent, __('Renter'), $this->price_type, $this->price_from, $this->customer_commission);

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

            $commissionDescription = __("property_trans.acquisition.commission.{$tranKey}", [
                'percentage' => number_format_tran($commission).'%',
            ]);
        }

        if ($priceType == PropertyPriceType::Fix && $priceFrom > 0) {
            $total = number_format($priceFrom * $commission / 100);
            $totalDescription = " ({$total})";
        }

        return __('property_trans.acquisition.commission.by', [
            'commission' => $commissionDescription,
            'commission_by' => $by,
        ]).$totalDescription;
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Model $model) {
            $acquisitionType = $model->acquisition_type;
            $model->code = str(Str::random(3))->upper()->toString().get_random_digit(6);
            $townshipSlug = $model->township->slug;
            $model->slug = $model->type->getSlug().'-'.$acquisitionType->getSlug().'-in-'.$townshipSlug.'-'.$model->code;
        });

        static::addGlobalScope('agent', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user instanceof Admin && $user->hasRole('Agent')) {
                    $builder->whereHas('leads', function (Builder $q) use ($user) {
                        $q->where('admin_id', $user->id);
                    });
                }
            }
        });
    }
}
