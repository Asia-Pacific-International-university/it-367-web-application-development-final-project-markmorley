<?php

namespace App\Http\Requests;

use App\Models\MemberProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreMemberProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', MemberProfile::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'member'),
                Rule::unique('member_profiles', 'user_id'),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:member_profiles,student_id'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
