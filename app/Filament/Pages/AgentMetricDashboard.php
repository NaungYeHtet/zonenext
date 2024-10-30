<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class AgentMetricDashbaord extends BaseDashboard
{
    use HasFiltersAction;

    protected static string $routePath = '/agent-metric';

    public static function getNavigationLabel(): string
    {
        return __('Agent metric');
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('startDate'),
                    DatePicker::make('endDate'),
                    // ...
                ]),
        ];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
