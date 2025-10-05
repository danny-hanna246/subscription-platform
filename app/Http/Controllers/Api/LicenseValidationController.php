<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateLicenseRequest;
use App\Services\LicenseValidationService;

class LicenseValidationController extends Controller
{
    protected $validationService;

    public function __construct(LicenseValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function validate(ValidateLicenseRequest $request)
    {
        $result = $this->validationService->validate(
            $request->license_key,
            $request->device_id,
            $request->device_info
        );

        return response()->json($result, $result['response_code']);
    }
}
