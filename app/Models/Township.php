<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Township extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'state_id',
        'code',
        'slug',
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'state_id' => 'integer',
        'name' => 'array',
    ];

    // Scope for search functionality
    public function scopeSearch(Builder $query, $search)
    {
        if ($search) {
            $keywords = explode(' ', $search);

            $query->where(function (Builder $q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where('name->en', 'like', "%{$keyword}%")
                        ->orWhere('name->my', 'like', "%{$keyword}%");
                }
            });
        }
    }

    // Scope for filtering by township
    public function scopeFilterState(Builder $query, $state)
    {
        if ($state) {
            $query->whereRelation('state', 'slug', $state);
        }
    }

    public function scopeFilterSlug(Builder $query, $slug)
    {
        if ($slug) {
            $query->where('slug', $slug);
        }
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

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
}
