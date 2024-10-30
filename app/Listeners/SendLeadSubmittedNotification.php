<?php

namespace App\Listeners;

use App\Enums\LeadStatus;
use App\Events\LeadSubmitted;
use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $admin = Admin::leadAssignment($lead)->first();

        if ($admin) {
            $lead->update([
                'admin_id' => $admin->id,
                'status' => LeadStatus::Assigned,
            ]);
            $admin->notify(new \App\Notifications\LeadAssignedNotification($lead));

            return;
        }
    }
}
