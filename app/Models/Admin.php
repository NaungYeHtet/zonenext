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
        'preferred_notification_channels' => 'array',
        'preferred_lead_types' => 'array',
        'preferred_property_types' => 'array',
        'preferred_townships' => 'array',
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

    public function scopeAgent(Builder $query)
    {
        $query->whereRelation('roles', 'name', 'Agent');
    }

    public function scopeFallbackAgent(Builder $query, bool $condition)
    {
        if ($condition) {
            $query->where('email', '=', 'naungyehtet.fallbackagent@gmail.com');
        } else {
            $query->where('email', '!=', 'naungyehtet.fallbackagent@gmail.com');
        }
    }

    public function scopeLeadAssignment(Builder $query, ?Lead $lead)
    {
        if ($lead) {
            if ($lead->property_id) {
                $query->where('id', $lead->property->owner->admin_id);
            } else {
                $query->withCount('leads')
                    ->agent()
                    ->fallbackAgent(false)
                    ->preferredLeadTypes($lead->interest, $lead->is_owner)
                    ->preferredPropertyTypes($lead->property_type)
                    ->preferredTownships($lead->township_id)
                    ->orderBy('leads_count', 'asc');
            }
        }
    }

    public function scopePreferredLeadTypes(Builder $query, ?LeadInterest $leadInterest = null, ?bool $isOwner = null)
    {
        if ($leadInterest && $isOwner !== null) {
            $query->whereJsonContains('preferred_lead_types', $leadInterest->getLeadType($isOwner)->value)
                ->orWhereNull('preferred_lead_types');
        }
    }

    public function scopePreferredPropertyTypes(Builder $query, ?PropertyType $propertyType = null)
    {
        if ($propertyType) {
            $query->whereJsonContains('preferred_property_types', $propertyType->value)
                ->orWhereNull('preferred_property_types');
        }
    }

    public function scopePreferredTownships(Builder $query, $townshipId)
    {
        if ($townshipId) {
            $query->whereJsonContains('preferred_townships', (string)$townshipId)
                ->orWhereNull('preferred_townships');
        }
    }

    public static function getLeadAssigmentAgent(Lead $lead): Admin
    {
        $agent = Admin::leadAssignment($lead)->first();

        if (!$agent) {
            $agent = Admin::fallbackAgent(true)->first();
        }
        return $agent;
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {

                if ($value == null || is_valid_url($value)) {
                    return asset('images/avatars/admin.png');
                }

                // return asset('images/avatars/admin.png');

                return Storage::disk('public')->url($value);
            }
        );
    }
}
