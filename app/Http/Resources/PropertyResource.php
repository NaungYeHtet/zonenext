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
            'coverImage' => $this->cover_image,
            'price' => $this->price_detail,
            'address' => $this->address,
            'gallery' => $this->documents->map(fn ($item) => filter_var($item->document, FILTER_VALIDATE_URL) ? $item->document : Storage::disk('public')->url($item->document)),
            'posted_at' => $this->posted_at->shortRelativeDiffForHumans(),
        ];
    }
}
