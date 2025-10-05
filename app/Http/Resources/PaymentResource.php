<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'gateway' => $this->gateway,
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'receipt_url' => $this->receipt_url,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
