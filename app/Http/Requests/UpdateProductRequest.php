<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:100',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:120',
                Rule::unique('products', 'slug')->ignore($this->product),
            ],
            'description' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];
    }
}
