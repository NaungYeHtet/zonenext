<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model implements Eventable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lead_id',
        'date',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'lead_id' => 'integer',
        'date' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    public function toEvent(): Event|array
    {
        return Event::make($this)
            ->title($this->lead->name)
            ->start($this->date)
            ->end($this->date);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status', AppointmentStatus::Pending);
    }
}
