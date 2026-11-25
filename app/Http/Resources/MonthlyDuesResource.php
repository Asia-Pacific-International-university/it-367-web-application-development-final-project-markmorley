<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyDuesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'required_amount' => $this->required_amount,
            'allocated_total' => $this->whenHas(
                'allocated_total',
                fn ($total) => number_format((float) $total, 2, '.', '')
            ),
            'status' => $this->whenHas(
                'allocated_total',
                fn ($total) => (float) $total >= (float) $this->required_amount ? 'paid' : 'unpaid'
            ),
            'member' => $this->whenLoaded('memberProfile', fn () => [
                'id' => $this->memberProfile->id,
                'full_name' => $this->memberProfile->full_name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
