<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Setting
 */
class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group' => $this->group,
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type->value,
            'value' => $this->typedValue(),
        ];
    }
}
