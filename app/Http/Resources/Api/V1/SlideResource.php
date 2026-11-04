<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Slide
 */
class SlideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => Storage::disk('public')->url($this->image),
            'link_url' => $this->link_url,
            'sort_order' => $this->sort_order,
        ];
    }
}
