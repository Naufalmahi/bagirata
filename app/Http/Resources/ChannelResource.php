<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'session_count' => $this->whenCounted('sessions'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
