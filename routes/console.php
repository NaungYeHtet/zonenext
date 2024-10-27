<?php

use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    // $this->comment(Inspiring::quote());
    // dd(PropertyType::getOptions());

    // dd(Property::latest()->first()->cover_image, Property::first()->cover_image);
    $owner = User::all()->random();
    dd($owner);
})->purpose('Display an inspiring quote')->hourly();
