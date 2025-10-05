<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_key' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'device_info' => 'nullable|string',
        ];
    }
}
