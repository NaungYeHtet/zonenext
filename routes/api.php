<?php

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyFilterController;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum', EnsureEmailIsVerified::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth:sanctum');
});

Route::prefix('/inquiry')->controller(InquiryController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'submit');
});

Route::prefix('property-filters')->controller(PropertyFilterController::class)->group(function () {
    Route::get('/', 'index'); // list-types, types, states, price-ranges
    Route::get('/townships', 'townships');
});

Route::prefix('properties')->controller(PropertyController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{property}', 'show');
});

Route::get('groups', GroupController::class);
