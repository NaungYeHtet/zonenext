<?php

namespace App\Filament\Widgets;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\ChartWidget;

class AgentCommissionChartWidget extends ChartWidget
{
    use FilamentWidgetHelper;

    public function getHeading(): string
    {
        return __('Commission');
    }

    public function getColor(): string
    {
        return 'danger';
    }

    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $agent = $this->getAgent();

        $leadQuery = Lead::when($agent, function ($query) use ($agent) {
            $query->where('admin_id', $agent->id);
        });

        [$startDate, $endDate] = $this->getDateRanges();

        $leads = $leadQuery
            ->join('properties', 'leads.property_id', '=', 'properties.id')
            ->where('leads.status', LeadStatus::Converted)
            ->whereIn('leads.interest', [LeadInterest::Buying, LeadInterest::Renting])
            ->where('leads.is_owner', false)
            ->whereDate('leads.created_at', '>=', $startDate)
            ->whereDate('leads.created_at', '<=', $endDate)
            ->selectRaw('DATE(leads.created_at) as date, SUM(properties.purchased_commission) as commission')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        foreach ($leads as $lead) {
            $labels[] = $lead['date'];
            $data[] = $lead['commission'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('Commission'),
                    'data' => $data,
                    'backgroundColor' => '#eb4034',
                    'borderColor' => '#eb4034',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
