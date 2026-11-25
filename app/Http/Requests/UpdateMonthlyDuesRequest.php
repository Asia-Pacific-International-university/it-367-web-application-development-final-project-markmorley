<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonthlyDuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('monthly_due'));
    }

    public function rules(): array
    {
        return [
            'required_amount' => ['required', 'numeric', 'gt:0', 'max:999999.99', 'decimal:0,2'],
        ];
    }
}
