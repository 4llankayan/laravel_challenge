<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price' => [
                'required',
                'int',
                'min:0'
            ],
            'quantity' => [
                'required',
                'int',
                'min:0'
            ],
            'description' => [
                'string',
                'max:255',
            ]
        ];
    }
}
