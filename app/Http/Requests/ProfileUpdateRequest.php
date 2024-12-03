<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [ 'required','string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'governorate' => ['nullable', 'string'],
            'experience_year' => ['nullable', 'integer','min:0'],
            'fees' => ['nullable', 'numeric','min:0'],
            'notes' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ];
        }
        public function messages(): array
        {
            return [
                'email.unique' => 'The email has already been taken.',
            ];
        }
        public function attributes(): array
        {
            return [
                'name' => 'name',
                'email' => 'email',
                'bio' => 'bio',
                'phone' => 'phone',
                'governorate' => 'governorate',
                'experience_year' => 'experience_year',
                'fees' => 'fees',
                'notes' => 'notes',
                'medical_history' => 'medical_history',
            ];
        }
}
