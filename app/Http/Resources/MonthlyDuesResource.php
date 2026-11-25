<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyDuesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasTotal = array_key_exists('allocated_total', $this->resource->getAttributes());
        $total = $hasTotal ? (float) $this->allocated_total : 0.0;

        return [
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'required_amount' => $this->required_amount,
            'allocated_total' => $this->when(
                $hasTotal,
                fn () => number_format($total, 2, '.', '')
            ),
            'status' => $this->when(
                $hasTotal,
                fn () => $total >= (float) $this->required_amount ? 'paid' : 'unpaid'
            ),
            'member' => $this->whenLoaded('memberProfile', fn () => [
                'id' => $this->memberProfile->id,
                'full_name' => $this->memberProfile->full_name,
            ]),
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($payment) => [
                'id' => $payment->id,
                'payment_date' => $payment->payment_date?->toDateString(),
                'amount' => $payment->amount,
                'allocated_amount' => $payment->pivot->allocated_amount,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
