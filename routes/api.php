<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\PropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('properties')->controller(PropertyController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('groups', GroupController::class);
