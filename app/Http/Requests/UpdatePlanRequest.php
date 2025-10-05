<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:80',
            'slug' => 'sometimes|required|string|max:100',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|max:10',
            'duration_days' => 'sometimes|required|integer|min:1',
            'user_limit' => 'sometimes|required|integer|min:1',
            'device_limit' => 'sometimes|required|integer|min:1',
            'features' => 'nullable|array',
            'active' => 'boolean',
        ];
    }
}
