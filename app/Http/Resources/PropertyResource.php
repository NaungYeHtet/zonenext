<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'coverImage' => $this->cover_image_url,
            'price' => $this->price_detail,
            'address' => $this->address,
            'gallery' => $this->gallery,
            'squareFeet' => number_format($this->square_feet),
            'areaDescription' => $this->area_description,
            'bedroomsCount' => (int) $this->bedroomTypes()->sum('quantity'),
            'bathroomsCount' => $this->bathrooms_count,
            'postedAt' => $this->posted_at->shortRelativeDiffForHumans(),
        ];
    }
}
