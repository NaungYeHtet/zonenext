<?php

namespace App\Policies;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Models\Admin;
use App\Models\Lead;

class LeadPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_lead');
    }

    public function view(Admin $user, Lead $lead): bool
    {
        if ($user->hasRole('Agent') && $user->id === $lead->admin_id) {
            return true;
        }

        return $user->can('view_lead');
    }

    public function create(Admin $user): bool
    {
        return $user->hasRole('Agent');
    }

    public function createProperty(Admin $user, Lead $lead): bool
    {
        if (! in_array($lead->status, [LeadStatus::Scheduled, LeadStatus::UnderNegotiation])) {
            return false;
        }

        if (! $lead->is_owner || $lead->interest == LeadInterest::Buying) {
            return false;
        }

        return $user->hasRole('Agent') && $user->id === $lead->admin_id;
    }

    public function update(Admin $user, Lead $lead): bool
    {
        return $user->hasRole('Agent') && $user->id === $lead->admin_id;
    }

    public function delete(Admin $user, Lead $lead): bool
    {
        if (in_array($lead->status, [LeadStatus::Converted, LeadStatus::Closed])) {
            return false;
        }

        if ($user->hasRole('Agent') && $user->id === $lead->admin_id) {
            return true;
        }

        return $user->can('delete_lead');
    }
}
