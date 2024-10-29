<?php

namespace App\Models;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
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
}
