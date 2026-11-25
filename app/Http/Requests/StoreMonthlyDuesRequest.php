<?php

namespace App\Http\Requests;

use App\Models\MonthlyDues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMonthlyDuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', MonthlyDues::class);
    }

    public function rules(): array
    {
        return [
            'member_profile_id' => [
                'required',
                'integer',
                Rule::exists('member_profiles', 'id')->whereNull('deleted_at'),
            ],
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('monthly_dues')->where(
                    fn ($query) => $query
                        ->where('member_profile_id', $this->input('member_profile_id'))
                        ->where('year', $this->input('year'))
                ),
            ],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'required_amount' => ['sometimes', 'numeric', 'gt:0', 'max:999999.99', 'decimal:0,2'],
        ];
    }
}
