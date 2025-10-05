<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'license_key' => $this->license_key,
            'issued_at' => $this->issued_at?->toDateTimeString(),
            'expires_at' => $this->expires_at?->toDateTimeString(),
            'status' => $this->status,
            'is_valid' => $this->isValid(),
            'devices_count' => $this->whenCounted('devices'),
            'devices' => $this->whenLoaded('devices'),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
