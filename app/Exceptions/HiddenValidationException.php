<?php

namespace App\Exceptions;

use App\Traits\ResponseHelper;
use Exception;

class HiddenValidationException extends Exception
{
    use ResponseHelper;

    public function __construct(public array $errors) {}

    public function render()
    {
        return $this->responseHiddenValidationError($this->errors);
    }
}
