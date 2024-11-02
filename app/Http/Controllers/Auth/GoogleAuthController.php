<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'social_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    // 'avatar' => $googleUser->getAvatar(),
                    // other fields as needed
                ]
            );

            Auth::login($user);

            // Generate Sanctum token
            $token = $user->createToken('web')->plainTextToken;

            return $this->responseSuccess([
                'access_token' => $token,
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            throw $e;

            return response()->json(['error' => 'Failed to authenticate'], 500);
        }
    }
}
