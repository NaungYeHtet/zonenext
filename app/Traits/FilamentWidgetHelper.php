<?php

namespace App\Traits;

use App\Models\Admin;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait FilamentWidgetHelper
{
    use InteractsWithPageFilters;

    public function getAgent(): ?Admin
    {
        $authUser = Filament::auth()->user();

        if (! $authUser instanceof Admin) {
            abort(403);
        }

        if ($authUser->hasRole('Agent')) {
            return $authUser;
        }

        if (array_key_exists('agent_id', $this->filters)) {
            return Admin::find($this->filters['agent_id']);
        }

        return null;
    }

    public function getDateRanges(): array
    {
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->format('Y-m-d 23:59:59');
        if (array_key_exists('date_range', $this->filters) && $this->filters['date_range']) {
            $dates = explode(' - ', $this->filters['date_range']);

            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d 23:59:59');
        }

        return [$startDate, $endDate];
    }
}
