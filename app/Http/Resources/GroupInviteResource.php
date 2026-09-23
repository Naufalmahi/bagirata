<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $frontUrl = config('app.frontend_url', url('/'));

        return [
            'id' => $this->id,
            'token' => $this->token,
            'active' => $this->active,
            'usable' => $this->usable(),
            'used_count' => $this->used_count,
            'max_uses' => $this->max_uses,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'uses_label' => $this->usesSummary(),
            'invalid_reason' => $this->invalidReason(),
            'url' => $this->usable() ? $frontUrl.'/join/'.$this->token : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
