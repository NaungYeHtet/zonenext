<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    // $this->comment(Inspiring::quote());

    dd(User::count());
})->purpose('Display an inspiring quote')->hourly();
