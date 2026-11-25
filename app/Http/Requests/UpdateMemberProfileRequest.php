<?php

namespace App\Http\Requests;

use App\Models\MemberProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateMemberProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('member'));
    }

    public function rules(): array
    {
        $member = $this->route('member');

        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'student_id' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('member_profiles', 'student_id')->ignore($member),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
        ];
    }
}
