<?php
// app/Exceptions/ApiException.php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errorData;

    public function __construct(
        $message = 'An error occurred',
        $statusCode = 400,
        $errorCode = null,
        $errorData = []
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->errorData = $errorData;
    }

    public function render($request)
    {
        $response = [
            'success' => false,
            'error' => $this->errorCode ?? 'API_ERROR',
            'message' => $this->getMessage(),
        ];

        if (!empty($this->errorData)) {
            $response['data'] = $this->errorData;
        }

        if (config('app.debug')) {
            $response['debug'] = [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString(),
            ];
        }

        return response()->json($response, $this->statusCode);
    }
}

// أمثلة على استخدامات مخصصة
class ApiKeyExpiredException extends ApiException
{
    public function __construct()
    {
        parent::__construct('API key has expired', 401, 'API_KEY_EXPIRED');
    }
}

class ApiKeyInvalidException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Invalid API key provided', 401, 'API_KEY_INVALID');
    }
}

class IpNotAllowedException extends ApiException
{
    public function __construct($ip)
    {
        parent::__construct(
            'IP address not allowed',
            403,
            'IP_NOT_ALLOWED',
            ['ip' => $ip]
        );
    }
}

class InsufficientPermissionsException extends ApiException
{
    public function __construct($scope)
    {
        parent::__construct(
            "This API key doesn't have '{$scope}' permission",
            403,
            'INSUFFICIENT_PERMISSIONS',
            ['required_scope' => $scope]
        );
    }
}
