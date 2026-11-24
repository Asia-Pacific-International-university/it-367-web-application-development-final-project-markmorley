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
            'amount' => $this->amount,
            'payment_date' => $this->payment_date?->toDateString(),
            'payment_method' => $this->payment_method,
            'member' => $this->whenLoaded('memberProfile', fn () => [
                'id' => $this->memberProfile->id,
                'full_name' => $this->memberProfile->full_name,
            ]),
            'recorded_by' => $this->whenLoaded('recordedBy', fn () => [
                'id' => $this->recordedBy->id,
                'name' => $this->recordedBy->name,
            ]),
            'monthly_dues' => $this->whenLoaded('monthlyDues', fn () => $this->monthlyDues->map(fn ($due) => [
                'id' => $due->id,
                'month' => $due->month,
                'year' => $due->year,
                'allocated_amount' => $due->pivot->allocated_amount,
            ])),
            'donation' => $this->whenLoaded('donation', fn ($donation) => $donation === null
                ? null
                : ['amount' => $donation->amount]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
