<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email_or_phone' => ['required', 'string'],
            'password' => ['required'],
        ];
    }
    public function attributes()
    {
        return [
            'email_or_phone' => 'Email or Phone',
            'password' => 'Password',
        ];
    }
    public function messages()
{
    return [
        'email_or_phone.required' => 'The email or phone is required.',
        'password.required' => 'The password is required.',
    ];
}
}
