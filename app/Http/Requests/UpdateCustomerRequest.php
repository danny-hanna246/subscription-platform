<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:150',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:200',
                Rule::unique('customers', 'email')->ignore($this->customer),
            ],
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:200',
            'address' => 'nullable|string',
            'meta' => 'nullable|array',
        ];
    }
}
