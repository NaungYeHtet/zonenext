<?php

use App\Enums\PropertyType;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    // $this->comment(Inspiring::quote());
    dd(PropertyType::getOptions());
})->purpose('Display an inspiring quote')->hourly();
