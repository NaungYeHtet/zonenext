<?php

namespace App\Models;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => LeadStatus::class,
        'property_type' => PropertyType::class,
        'interest' => LeadInterest::class,
        'preferred_contact_method' => LeadContactMethod::class,
        'preferred_contact_time' => LeadContactTime::class,
        'send_updates' => 'boolean',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name.' '.$this->last_name,
        );
    }

    protected function contact(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(', ', [$this->phone, $this->email]),
        );
    }

    public function scopeBuyer(Builder $query)
    {
        $query->where('interest', LeadInterest::Buying);
    }

    public function scopeSeller(Builder $query)
    {
        $query->where('interest', LeadInterest::Selling);
    }

    public function scopeLandlord(Builder $query)
    {
        $query->where('interest', LeadInterest::Renting)->where('is_owner', true);
    }

    public function scopeRenter(Builder $query)
    {
        $query->where('interest', LeadInterest::Renting)->where('is_owner', false);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('agent', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user instanceof Admin && $user->hasRole('Agent')) {
                    $builder->where('admin_id', $user->id);
                }
            }
        });
    }
}
