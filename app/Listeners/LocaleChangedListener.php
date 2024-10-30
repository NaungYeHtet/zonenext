<?php

namespace App\Listeners;

use App\Enums\Language;

class LocaleChangedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        auth()->user()->update([
            'language' => Language::from($event->locale)->value,
        ]);
    }
}
