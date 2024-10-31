<?php

namespace App\Filament\Widgets;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Models\Admin;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStatusWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $stats = [];
        $authUser = Filament::auth()->user();

        if (! $authUser instanceof Admin) {
            abort(403);
        }

        if (! ($this->filters)) {
            return [];
        }

        if ($authUser->hasRole('Agent')) {
            $agent = $authUser;
        } elseif (array_key_exists('agent_id', $this->filters)) {
            $agent = Admin::find($this->filters['agent_id']);
        }

        if (! $agent) {
            return [];
        }

        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->format('Y-m-d 23:59:59');
        if (array_key_exists('date_range', $this->filters) && $this->filters['date_range']) {
            $dates = explode(' - ', $this->filters['date_range']);

            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d 23:59:59');
        }

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

    protected function getLeadCount(Admin $agent, string $startDate, string $endDate, ?array $status = null)
    {
        $query = $agent->leads()->whereBetween('leads.created_at', [$startDate, $endDate]);

        if (! is_null($status)) {
            $query->whereIn('leads.status', $status);
        }

        return $query->count();
    }

    // Define a function to calculate total sale or rent value based on interest type
    protected function getTotalValue(Admin $agent, string $startDate, string $endDate, array $interestTypes, ?string $column = 'purchased_price')
    {
        return $agent->leads()
            ->whereBetween('leads.created_at', [$startDate, $endDate])
            ->join('properties', 'leads.property_id', '=', 'properties.id')
            ->where('leads.status', LeadStatus::Converted)
            ->whereIn('leads.interest', $interestTypes)
            ->where('leads.is_owner', false)
            ->sum("properties.{$column}");
    }
}
