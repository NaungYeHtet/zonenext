<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyBedroomType extends Model
{
    use HasFactory;

    protected $table = 'property_bedroom_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'bedroom_type_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'property_id' => 'integer',
        'bedroom_type_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bedroomType(): BelongsTo
    {
        return $this->belongsTo(BedroomType::class);
    }
}
