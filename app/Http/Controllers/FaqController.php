<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqResource;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __invoke()
    {
        return $this->responseSuccess([
            'faqs' => FaqResource::collection(Faq::all()),
        ]);
    }
}
