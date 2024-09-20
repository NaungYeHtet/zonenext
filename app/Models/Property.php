<?php

namespace App\Models;

use App\Enums\Filters\FilterListType;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Property extends Model
{
    use HasFactory, HasSlug, HasTranslations, SoftDeletes;

    public $translatable = ['title', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'township_id',
        'title',
        'description',
        'type',
        'slug',
        'status',
        'address',
        'latitude',
        'longitude',
        'posted_at',
        'sold_at',
        'rent_at',
        'completed_at',
    ];

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
        'address' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'posted_at' => 'datetime',
        'sold_at' => 'datetime',
        'rent_at' => 'datetime',
        'completed_at' => 'datetime',
        'type' => PropertyType::class,
        'status' => PropertyStatus::class,
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
        $query->where('status', PropertyStatus::Posted->value)->whereHas('acquisitions');
    }

    // Scope for list type filtering (Newest, ForSale, ForRent)
    public function scopeFilterListType(Builder $query, $listType)
    {
        $filterListType = FilterListType::from($listType);

        if ($filterListType == FilterListType::Newest) {
            $query->orderBy('posted_at', 'desc');
        } elseif ($filterListType == FilterListType::ForSale) {
            $query->whereHas('acquisitions', fn (Builder $q) => $q->where('type', PropertyAcquisitionType::Sell->value));
        } elseif ($filterListType == FilterListType::ForRent) {
            $query->whereHas('acquisitions', fn (Builder $q) => $q->where('type', PropertyAcquisitionType::Rent->value));
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
        $query->whereHas('acquisitions', function (Builder $q) use ($priceFrom, $priceTo) {
            if ($priceFrom) {
                $q->where('price_from', '>=', $priceFrom * 100000);
            }

            if ($priceTo) {
                $q->where('price_from', '<=', $priceTo * 100000);
            }
        });
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
        return $this->belongsToMany(BedroomType::class)
            ->using(PropertyBedroomType::class)
            ->as('property_bedroom_type')
            ->withPivot('id', 'quantity')
            ->withTimestamps();
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class)
            ->using(AgentProperty::class)
            ->as('agent_property')
            ->withPivot('id')
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

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }

    public function acquisitions(): HasMany
    {
        return $this->hasMany(PropertyAcquisition::class);
    }

    /**
     * Accessors
     */
    protected function priceDetail(): Attribute
    {
        return Attribute::make(
            get: function () {
                $acquisitions = $this->acquisitions()->get();

                $detail = [];

                foreach ($acquisitions as $acquisition) {
                    $detail[$acquisition->type->value] = $acquisition->price." ({$acquisition->type->getLabel()})";
                }

                return $detail;
            },
        );
    }
}
