<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'payment' => $this->whenLoaded('payment', fn () => [
                'id' => $this->payment->id,
                'payment_date' => $this->payment->payment_date?->toDateString(),
                'payment_method' => $this->payment->payment_method,
                'member' => $this->payment->memberProfile === null ? null : [
                    'id' => $this->payment->memberProfile->id,
                    'full_name' => $this->payment->memberProfile->full_name,
                ],
            ]),
        ];
    }
}
