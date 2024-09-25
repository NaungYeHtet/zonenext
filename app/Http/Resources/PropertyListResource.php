<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyListResource extends JsonResource
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
            'cover_image' => $this->cover_image,
            'price' => $this->price_detail,
            'address' => $this->address,
            'posted_at' => $this->posted_at->shortRelativeDiffForHumans(),
        ];
    }
}
