<?php

namespace App\Listeners;

use App\Events\LeadSubmitted;
use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

class SendLeadSubmittedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeadSubmitted $event): void
    {
        $lead = $event->lead;
        $admin = Admin::withCount('leads')->whereRelation('roles', 'name', 'Agent')->where(function (Builder $query) use ($lead) {
            $query->whereJsonContains('preferred_lead_interests', $lead->interest->value)
                ->orWhere('preferred_lead_interests', null);
        })->where(function (Builder $query) use ($lead) {
            $query->whereJsonContains('preferred_property_types', $lead->property_type->value)
                ->orWhere('preferred_property_types', null);
        })->where(function (Builder $query) use ($lead) {
            if ($lead->township_id) {
                $query->whereJsonContains('preferred_townships', $lead->township_id)
                    ->orWhere('preferred_townships', null);
            }
        })->orderBy('leads_count', 'asc')->first();

        if ($admin) {
            $lead->update([
                'admin_id' => $admin->id,
            ]);
            $admin->notify(new \App\Notifications\LeadAssignedNotification($lead));

            return;
        }
    }
}
