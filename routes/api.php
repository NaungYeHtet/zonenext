<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyFilterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ViewController;
use App\Http\Middleware\AuthOptionalMiddleware;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum', EnsureEmailIsVerified::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');
});

Route::prefix('/view')->middleware(AuthOptionalMiddleware::class . ':user')->controller(ViewController::class)->group(function () {
    Route::get('/property/{property}', 'property');
});

Route::prefix('/review')->middleware(AuthOptionalMiddleware::class . ':user')->controller(ReviewController::class)->group(function () {
    Route::post('/property', 'property');
});

Route::prefix('/inquiry')->controller(InquiryController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'submit')->middleware(AuthOptionalMiddleware::class . ':user');
    Route::post('/property', 'submitProperty')->middleware(AuthOptionalMiddleware::class . ':user');
});

Route::prefix('property-filters')->controller(PropertyFilterController::class)->group(function () {
    Route::get('/', 'index'); // list-types, types, states, price-ranges
    Route::get('/townships', 'townships');
});

Route::prefix('properties')->controller(PropertyController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{property}', 'show');
});

Route::prefix('agents')->controller(AgentController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('faqs', FaqController::class);

Route::get('groups', GroupController::class);
