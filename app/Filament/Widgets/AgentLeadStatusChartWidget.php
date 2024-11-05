<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Traits\FilamentWidgetHelper;
use Filament\Widgets\ChartWidget;

class AgentLeadStatusChartWidget extends ChartWidget
{
    use FilamentWidgetHelper;

    public function getHeading(): string
    {
        return __('Lead').' '.__('Status');
    }

    public function getColor(): string
    {
        return 'gray';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $agent = $this->getAgent();
        $backgroundColor = [];

        [$startDate, $endDate] = $this->getDateRanges();

        foreach (LeadStatus::cases() as $status) {
            if ($status == LeadStatus::New) {
                continue;
            }

            $labels[] = $status->getLabel();
            $data[] = Lead::when($agent, function ($query) use ($agent) {
                $query->where('admin_id', $agent->id);
            })
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('status', $status->value)->count();
            $backgroundColor[] = $status->getHexColor();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('Lead status'),
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#34eb6b',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
