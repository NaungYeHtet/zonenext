<?php

namespace App\Models;

use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAcquisition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'type',
        'price_type',
        'price_from',
        'price_to',
        'negotiable',
        'owner_commission',
        'customer_commission',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'property_id' => 'integer',
        'negotiable' => 'boolean',
        'owner_commission' => 'decimal:2',
        'customer_commission' => 'decimal:2',
        'type' => PropertyAcquisitionType::class,
        'price_type' => PropertyPriceType::class,
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
