<?php

namespace App\Models;

use App\Enums\Language;
use App\Enums\Lead\LeadInterest;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class Agent extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferred_property_types' => 'array',
        'preferred_lead_interests' => 'array',
        'preferred_townships' => 'array',
        'preferred_notification_channels' => 'array',
        'language' => Language::class,
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class)
            ->using(AgentProperty::class)
            ->as('agent_property')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function scopePreferredLeadInterests(Builder $query, ?LeadInterest $leadInterest = null)
    {
        if ($leadInterest) {
            $query->whereJsonContains('preferred_lead_interests', $leadInterest->value)
                ->orWhere('preferred_lead_interests', null);
        }
    }

    public function scopePreferredPropertyTypes(Builder $query, ?PropertyType $propertyType = null)
    {
        if ($propertyType) {
            $query->whereJsonContains('preferred_property_types', $propertyType->value)
                ->orWhere('preferred_property_types', null);
        }
    }

    public function scopePreferredTownships(Builder $query, $townshipId)
    {
        if ($townshipId) {
            $query->whereJsonContains('preferred_townships', $townshipId)
                ->orWhere('preferred_townships', null);
        }
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {

                if ($value == null) {
                    return null;
                }

                return is_valid_url($value) ? $value : Storage::disk('public')->url($value);
            }
        );
    }

    protected static function booted(): void
    {
        static::created(function (Agent $agent) {
            $agent->assignRole('Agent');
        });
    }
}
