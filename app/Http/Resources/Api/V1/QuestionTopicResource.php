<?php

namespace App\Http\Resources\Api\V1;

use App\Models\QuestionTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuestionTopic
 */
class QuestionTopicResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'faqs_count' => $this->whenCounted('faqs'),
            'questions_count' => $this->whenCounted('questions'),
        ];
    }
}
