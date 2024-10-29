<?php

use App\Enums\PropertyType;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Township;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    // $this->comment(Inspiring::quote());
    // dd(PropertyType::getOptions());

    // dd(Property::latest()->first()->cover_image, Property::first()->cover_image);
    // $township = Township::first();

    // dd($township->getTranslations('name', ['en']));
    // dd($owner);
    dd(Lead::whereNotNull('property_id')->count());
})->purpose('Display an inspiring quote')->hourly();
