<?php

namespace App\Models;

use App\Enums\Language;
use App\Enums\Lead\LeadInterest;
use App\Enums\PropertyType;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, HasPanelShield, HasRoles, Notifiable;

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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeLeadAssignment(Builder $query, ?Lead $lead)
    {
        if ($lead) {
            $query->withCount('leads')
                ->whereRelation('roles', 'name', 'Agent')
                ->preferredLeadTypes($lead->interest, $lead->is_owner)
                ->preferredPropertyTypes($lead->property_type)
                ->preferredTownships($lead->township_id)
                ->orderBy('leads_count', 'asc');
        }
    }

    public function scopePreferredLeadTypes(Builder $query, ?LeadInterest $leadInterest = null, ?bool $isOwner = null)
    {
        if ($leadInterest && $isOwner != null) {
            $query->whereJsonContains('preferred_lead_types', $leadInterest->getLeadType($isOwner)->value)
                ->orWhere('preferred_lead_types', null);
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
}
