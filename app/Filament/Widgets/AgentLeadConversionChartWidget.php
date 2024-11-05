<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\ChartWidget;

class AgentLeadConversionChartWidget extends ChartWidget
{
    use FilamentWidgetHelper;

    public function getHeading(): string
    {
        return __('Converted');
    }

    public function getColor(): string
    {
        return 'succes';
    }

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
            ->where('status', LeadStatus::Converted->value)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        foreach ($leads as $lead) {
            $labels[] = $lead['date'];
            $data[] = $lead['count'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('Converted'),
                    'data' => $data,
                    'backgroundColor' => '#34eb6b',
                    'borderColor' => '#34eb6b',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
