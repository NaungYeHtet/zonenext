<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgentCommissionChartWidget;
use App\Filament\Widgets\AgentLeadAssignedChartWidget;
use App\Filament\Widgets\AgentLeadConversionChartWidget;
use App\Filament\Widgets\AgentLeadStatusChartWidget;
use App\Filament\Widgets\AgentStatusWidget;
use App\Filament\Widgets\PropertyStatusWidget;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            PropertyStatusWidget::class,
            AgentStatusWidget::class,
            // AgentCommissionChartWidget::class,
            // AgentLeadAssignedChartWidget::class,
            // AgentLeadStatusChartWidget::class,
            // AgentLeadConversionChartWidget::class,
        ];
    }

    public function filtersForm(Form $form): Form
    {
        $authUser = Filament::auth()->user();

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DateRangePicker::make('date_range')
                            ->startDate(now()->startOfMonth())
                            ->endDate(now()->addMonth())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
