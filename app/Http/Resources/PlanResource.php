<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'duration_days' => $this->duration_days,
            'user_limit' => $this->user_limit,
            'device_limit' => $this->device_limit,
            'features' => $this->features,
            'active' => $this->active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
