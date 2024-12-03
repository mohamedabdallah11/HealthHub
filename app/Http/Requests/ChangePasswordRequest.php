<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'oldPassword' => ['required', 'string', 'min:8'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],      
          ];
    }
 /*    public function messages()
    {
         return [
           'oldPassword.required' => 'Old password is required.',
            'newPassword.required' => 'New password is required.',
            'newPassword.min' => 'New password must be at least 8 characters.',
            'newPassword.confirmed' => 'The new password confirmation does not match.',
        ]; 
    } */
}
