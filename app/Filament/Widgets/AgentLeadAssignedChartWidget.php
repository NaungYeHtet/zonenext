<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\ChartWidget;

class AgentLeadAssignedChartWidget extends ChartWidget
{
    use FilamentWidgetHelper;

    protected static ?string $heading = 'Chart';

    public function getHeading(): string
    {
        return __('Assigned');
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

        [$startDate, $endDate] = $this->getDateRanges();

        $leads = Lead::when($agent, function ($query) use ($agent) {
            $query->where('admin_id', $agent->id);
        })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(leads.created_at) as date, COUNT(leads.id) as count')
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
                    'label' => __('Assigned'),
                    'data' => $data,
                    'backgroundColor' => '#34eb6b',
                    'borderColor' => '#34eb6b',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
