<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'store'])->middleware('guest', 'throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/email/verify', [VerifyEmailController::class, 'verify'])
        ->middleware('throttle:6,1')
        ->name('verification.verify');
});

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->withoutMiddleware(LocalizationMiddleware::class);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->withoutMiddleware(LocalizationMiddleware::class);
