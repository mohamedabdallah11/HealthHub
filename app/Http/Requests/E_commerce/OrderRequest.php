<?php

namespace App\Http\Requests\E_commerce;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id', 
            'products.*.quantity' => 'required|integer|min:1',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|digits:11|regex:/^01[0-9]{9}$/'
        ];
    }
}
