<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => [
                '50' => '#f2f6fc',
                '100' => '#e1eaf8',
                '200' => '#cadbf3',
                '300' => '#a6c4ea',
                '400' => '#7ca5de',
                '500' => '#5d86d4',
                '600' => '#496cc7',
                '700' => '#3f5ab6',
                '800' => '#384b95',
                '900' => '#324176',
                '950' => '#222a49',
            ],
            'primary' => [
                '50' => '#f5f7fa',
                '100' => '#eaeef4',
                '200' => '#d0dae7',
                '300' => '#a7bad2',
                '400' => '#7896b8',
                '500' => '#5779a0',
                '600' => '#436086',
                '700' => '#344966',
                '800' => '#31435b',
                '900' => '#2c394e',
                '950' => '#1e2633',
            ],
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Relation::enforceMorphMap([
            'property' => \App\Models\Property::class,
            'user' => \App\Models\User::class,
            'project' => \App\Models\Project::class,
        ]);

        JsonResource::withoutWrapping();

        // filament configurations
        Field::configureUsing(function (Field $field) {
            $field->translateLabel();
        });
        Column::configureUsing(function (Column $field) {
            $field->translateLabel();
        });
        Entry::configureUsing(function (Entry $field) {
            $field->translateLabel();
        });
        Tab::configureUsing(function (Tab $field) {
            $field->translateLabel();
        });
        Fieldset::configureUsing(function (Fieldset $field) {
            $field->translateLabel();
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'my'])
                ->flags([
                    'en' => asset('flags/english.png'),
                    'my' => asset('flags/myanmar.png'),
                ])
                ->circular();
        });
    }
}
