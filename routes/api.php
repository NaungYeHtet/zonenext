<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyFilterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
