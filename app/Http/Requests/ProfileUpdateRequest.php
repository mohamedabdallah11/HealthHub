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
            'name' => [ 'string', 'max:255'],
            'email' => [ 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'phone' => ['nullable', 'string', 'unique:users,phone,'. $this->user()->id],
            'gender' => ['nullable', 'string','in:male,female'],
            'age' => ['nullable', 'integer','min:0'],
            //for doctors
            'experience_year' => ['nullable', 'integer','min:0'],
            'specialty' => ['nullable', 'string'],
            'fees' => ['nullable', 'numeric','min:0'],
            'bio' => ['nullable', 'string'],
            'clinicaddress' => ['nullable', 'string'],
            'clinicgovernate' => ['nullable', 'string'],
            'clinicname' => ['nullable', 'string'],
            'speciality' => ['nullable', 'string'],
            //for clients
            'notes' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string'],
            'weight' => ['nullable', 'integer','min:0'],
            'height' => ['nullable', 'integer','min:0'],

            
        ];
        }
        public function messages(): array
        {
            return [
                'email.unique' => 'The email has already been taken.',
                'phone.unique' => 'Phone number already exists',

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
