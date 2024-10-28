<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab as FormsTab;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();
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
            'admin' => \App\Models\Admin::class,
            'agent' => \App\Models\Agent::class,
            'project' => \App\Models\Project::class,
            'lead' => \App\Models\Lead::class,
        ]);

        JsonResource::withoutWrapping();

        // filament configurations
        Field::configureUsing(function (Field $component) {
            $component->translateLabel();
        });
        Column::configureUsing(function (Column $component) {
            $component->translateLabel();
        });
        Entry::configureUsing(function (Entry $component) {
            $component->translateLabel();
        });
        Tab::configureUsing(function (Tab $component) {
            $component->translateLabel();
        });
        FormsTab::configureUsing(function (FormsTab $component) {
            $component->translateLabel();
        });
        Fieldset::configureUsing(function (Fieldset $component) {
            $component->translateLabel();
        });
        Section::configureUsing(function (Section $component) {
            $component->translateLabel();
        });
        Step::configureUsing(function (Step $component) {
            $component->translateLabel();
        });
        TableAction::configureUsing(function (TableAction $component) {
            $component->translateLabel();
        });

        Select::configureUsing(function (Select $component) {
            $component->native(false);
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'my'])
                ->flags([
                    'en' => asset('images/flags/english.png'),
                    'my' => asset('images/flags/myanmar.png'),
                ])
                ->circular();
        });
    }
}
