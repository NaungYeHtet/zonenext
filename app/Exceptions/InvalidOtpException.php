<?php

namespace App\Exceptions;

use App\Traits\ResponseHelper;
use Exception;

class InvalidOtpException extends Exception
{
    use ResponseHelper;

    public function render()
    {
        return $this->responseError(message: __('auth.invalid_otp'), status: 400);
    }
}
