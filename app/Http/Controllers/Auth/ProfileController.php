<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function show(Request $request)
    {
        return $this->responseSuccess([
            'user' => new UserResource($request->user()),
        ]);
    }
}
