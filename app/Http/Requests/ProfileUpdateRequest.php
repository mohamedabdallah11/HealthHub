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
        return $this->user()->id==$this->route('user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'governorate' => ['nullable', 'string'],
            'experinece_year' => ['nullable', 'integer','min:0'],
            'fees' => ['nullable', 'numeric','min:0'],
            'notes' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ];
        }
}
