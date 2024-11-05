<?php

namespace App\Filament\Widgets;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyStatusWidget extends BaseWidget
{
    use FilamentWidgetHelper;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        [$startDate, $endDate] = $this->getDateRanges();

        return [
            Stat::make(__('Properties created'), number_format(Property::count()))
                ->color('success'),
            Stat::make(__('Properties posted'), number_format(Property::where('status', PropertyStatus::Posted)->count()))
                ->color('success'),
            Stat::make(__('Sale'), number_format(Property::sale()->count()))
                ->color('success'),
            Stat::make(__('Rent'), number_format(Property::rent()->count()))
                ->color('success'),
        ];
    }
}
