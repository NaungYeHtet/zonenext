<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasPermissions;

class Agent extends Authenticatable
{
    use HasFactory, HasPermissions;

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
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class)
            ->using(AgentProperty::class)
            ->as('agent_property')
            ->withPivot('id')
            ->withTimestamps();
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
