<?php

namespace App\Models;

use App\Enums\GroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Group extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'updatable',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'array',
        'updatable' => 'boolean',
        'type' => GroupType::class,
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
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Scopes
     */
    public function scopeFilterType(Builder $query, GroupType $type)
    {
        $query->where('type', $type);
    }

    /**
     * Relationships
     */
    public function properties(): MorphToMany
    {
        return $this->morphedByMany(Property::class, 'groupable');
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'groupable');
    }
}
