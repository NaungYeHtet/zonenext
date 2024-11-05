<?php

namespace App\Filament\Widgets;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Models\Admin;
use App\Models\Lead;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStatusWidget extends BaseWidget
{
    use FilamentWidgetHelper;

    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $stats = [];
        $agent = $this->getAgent();

        [$startDate, $endDate] = $this->getDateRanges();

        // Get the lead assigned and converted counts
        $leadAssigned = $this->getLeadCount($agent, $startDate, $endDate);
        $leadConverted = $this->getLeadCount($agent, $startDate, $endDate, [LeadStatus::Converted]);

        // Calculate the conversion rate
        $conversionRate = round($leadConverted > 0 ? $leadConverted / $leadAssigned * 100 : 0);

        // Calculate total sales and rental values
        $totalSaleValue = $this->getTotalValue($agent, $startDate, $endDate, [LeadInterest::Buying]);
        $totalRentValue = $this->getTotalValue($agent, $startDate, $endDate, [LeadInterest::Renting]);

        $commissionTotal = $this->getTotalValue($agent, $startDate, $endDate, [LeadInterest::Buying, LeadInterest::Renting], 'purchased_commission');

        return [
            Stat::make(__('Lead assigned'), number_format($leadAssigned))
                ->color('success'),
            Stat::make(__('Lead converted'), number_format($leadConverted))
                ->color('success'),
            Stat::make(__('Conversion rate'), "$conversionRate%")
                ->color('success'),
            Stat::make(__('Total Sale value'), number_format($totalSaleValue))
                ->color('success'),
            Stat::make(__('Total Rent value'), number_format($totalRentValue))
                ->color('success'),
            Stat::make(__('Total Commission'), number_format($commissionTotal))
                ->color('success'),
        ];

        return $stats;
    }

    protected function getLeadCount(?Admin $agent, string $startDate, string $endDate, ?array $status = null)
    {
        $query = Lead::when($agent, function ($query) use ($agent) {
            $query->where('admin_id', $agent->id);
        })->whereBetween('leads.created_at', [$startDate, $endDate]);

        if (! is_null($status)) {
            $query->whereIn('leads.status', $status);
        }

        return $query->count();
    }

    // Define a function to calculate total sale or rent value based on interest type
    protected function getTotalValue(?Admin $agent, string $startDate, string $endDate, array $interestTypes, ?string $column = 'purchased_price')
    {
        return Lead::when($agent, function ($query) use ($agent) {
            $query->where('admin_id', $agent->id);
        })
            ->whereBetween('leads.created_at', [$startDate, $endDate])
            ->join('properties', 'leads.property_id', '=', 'properties.id')
            ->where('leads.status', LeadStatus::Converted)
            ->whereIn('leads.interest', $interestTypes)
            ->where('leads.is_owner', false)
            ->sum("properties.{$column}");
    }
}
