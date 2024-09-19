<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\CommonMark\Node\Block\Document;
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

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

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
        return $this->hasMany(Document::class);
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }
}
