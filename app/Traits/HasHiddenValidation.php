<?php

namespace App\Traits;

use App\Exceptions\HiddenValidationException;
use Illuminate\Support\Facades\Validator;

trait HasHiddenValidation
{
    public function validateHidden(array $rules)
    {
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            throw new HiddenValidationException($validator->errors()->toArray());
        }
    }
}
