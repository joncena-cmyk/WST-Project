<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'student_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique(User::class, 'student_id')->ignore($this->user()->id),
            ],
            'program_id' => ['nullable', 'exists:programs,id'],
        ];
    }
}
