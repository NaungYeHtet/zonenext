<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgentStatusWidget;
use App\Models\Admin;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class AgentMetricDashbaord extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = '/agent-metric';

    public static function getNavigationLabel(): string
    {
        return __('Agent metric');
    }

    public function getWidgets(): array
    {
        return [
            AgentStatusWidget::class,
        ];
    }

    public function filtersForm(Form $form): Form
    {
        $authUser = Filament::auth()->user();

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('agent_id')
                            ->label('Agent')
                            ->options(\App\Models\Admin::agent()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->hidden(fn () => $authUser instanceof Admin && $authUser->hasRole('Agent')),
                        DateRangePicker::make('date_range')
                            ->startDate(now()->startOfMonth())
                            ->endDate(now()->addMonth())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
