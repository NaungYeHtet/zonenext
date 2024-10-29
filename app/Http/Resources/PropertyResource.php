<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'cover_image' => is_valid_url($this->cover_image) ? $this->cover_image : Storage::disk('public')->url($this->cover_image),
            'price' => $this->price_detail,
            'address' => $this->address,
            'gallery' => collect($this->images)->map(fn ($image) => is_valid_url($image) ? $image : Storage::disk('public')->url($image)),
            'square_feet' => number_format($this->square_feet),
            'area_description' => $this->area_description,
            'bedrooms_count' => (int) $this->bedroomTypes()->sum('quantity'),
            'bathrooms_count' => $this->bathrooms_count,
            'posted_at' => $this->posted_at->shortRelativeDiffForHumans(),
        ];
    }
}
