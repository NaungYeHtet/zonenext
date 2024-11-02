<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->responseError([
                'message' => __('auth.failed'),
            ], status: 422);
        }

        if (! password_verify($request->password, $user->password)) {
            return $this->responseError([
                'message' => __('auth.failed'),
            ], status: 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->responseSuccess([
            'access_token' => $token,
            'user' => new UserResource($user),
            'token_type' => 'Bearer',
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->responseSuccess(message: 'Logged out');
    }
}
