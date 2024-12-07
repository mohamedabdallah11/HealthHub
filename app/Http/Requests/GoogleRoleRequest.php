<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleRoleRequest extends FormRequest
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
            'role'=>['required','in:doctor,client'],
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'governorate' => ['nullable', 'string'], 
            'gender' => ['nullable', 'string','in:male,female'],
            'age' => ['nullable', 'integer','min:0'],

            ];
    }

    public function attributes()
    {
        return [
            'role' => 'Role',
            'phone' => 'Phone',
            'governorate' => 'Governorate', 
            'gender' => 'Gender',
            'age' => 'Age',
        ];
    }
    public function messages()
    {
        return [       
            
        'role.in' => 'Role must be either doctor, client',
        'gender.in' => 'Gender must be either male or female',
        'age.integer' => 'Age must be a number',
        'age.min' => 'Age must be at least 0',
        'phone.unique' => 'Phone number already exists',

        ];
    }



}
