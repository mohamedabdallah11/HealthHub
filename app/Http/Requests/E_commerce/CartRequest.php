<?php

namespace App\Http\Requests\E_commerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:10',

        ];
    }
}
