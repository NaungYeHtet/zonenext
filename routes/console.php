<?php

use App\Enums\PropertyType;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Township;
use App\Models\User;
use App\Notifications\LeadConvertedNotification;
use App\Notifications\PropertyCreatedNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $lead = Lead::find(229);

    $lead->notify(new PropertyCreatedNotification($lead->property));

    // $this->comment(Inspiring::quote());
    // dd(PropertyType::getOptions());

    // dd(Property::latest()->first()->cover_image, Property::first()->cover_image);
    // $township = Township::first();

    // dd($township->getTranslations('name', ['en']));
    // dd($owner);
    // dd(Lead::whereNotNull('property_id')->count());
    // $admin = Admin::find(39);

    // dd($admin->leads->pluck('property_id')->toArray());

    // $user = User::where('email', 'naungyehtet.zonenextuser@gmail.com')->first();

    // $lead = Lead::find(1550);

    // $lead->notify(new LeadConvertedNotification);

})->purpose('Display an inspiring quote')->hourly();
