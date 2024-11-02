<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class SignupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function store(SignupRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->responseSuccess([
            'access_token' => $token,
            'user' => new UserResource($user),
            'token_type' => 'Bearer',
        ]);
    }
}
