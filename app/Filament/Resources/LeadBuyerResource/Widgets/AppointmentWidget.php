<?php

namespace App\Filament\Resources\LeadBuyerResource\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use Filament\Facades\Filament;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AppointmentWidget extends CalendarWidget
{
    protected bool $eventClickEnabled = true;
    protected bool $dateClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit';

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        $authUser = Filament::auth()->user();
        $appointmentQuery = Appointment::where('status', AppointmentStatus::Pending);
        if ($authUser instanceof Admin && $authUser->hasRole('Agent')) {
            $appointmentQuery->whereRelation('lead', 'admin_id', $authUser->id);
        }
        return $appointmentQuery->get();
    }

    public function onDateClick(array $info = []): void
    {
        return;
    }
}
