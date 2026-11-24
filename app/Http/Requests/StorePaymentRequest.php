<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class);
    }

    public function rules(): array
    {
        return [
            'member_profile_id' => [
                'required',
                'integer',
                Rule::exists('member_profiles', 'id')->whereNull('deleted_at'),
            ],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999.99', 'decimal:0,2'],
            'payment_date' => ['required', 'date_format:Y-m-d'],
            'payment_method' => ['required', Rule::in(['cash', 'bank_transfer'])],
            'monthly_dues_ids' => ['required', 'array', 'min:1'],
            'monthly_dues_ids.*' => ['integer', 'distinct', 'exists:monthly_dues,id'],
        ];
    }
}
