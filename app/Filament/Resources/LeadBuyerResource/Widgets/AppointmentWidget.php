<?php

namespace App\Filament\Resources\LeadBuyerResource\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Database\Eloquent\Collection;

class AppointmentWidget extends CalendarWidget
{
    protected bool $eventClickEnabled = true;
    protected bool $dateClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit';

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        return Appointment::where('status', AppointmentStatus::Pending)->get();
    }

    public function onDateClick(array $info = []): void
    {
        return;
    }
}
