<?php

namespace App\Policies;

use App\Enums\PropertyStatus;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Property;

class PropertyPolicy
{
    public function viewAny(Admin|Agent $user): bool
    {
        return $user->can('view_property');
    }

    public function view(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('view_property');
    }

    public function create(Admin|Agent $user): bool
    {
        return $user->can('create_property');
    }

    public function update(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('update_property') && $property->status != PropertyStatus::Completed;
    }

    public function delete(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('delete_property') && ! $property->trashed();
    }

    public function restore(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('restore_property') && $property->trashed();
    }

    public function updatePosted(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        if ($user instanceof Agent) {
            return false;
        }

        return $user->can('posted_update::property::status') && $property->status == PropertyStatus::Draft;
    }

    public function updateSoldOut(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('sold_out_update::property::status') && ($property->status == PropertyStatus::Posted || $property->status == PropertyStatus::Rented) && $property->is_saleable;
    }

    public function updateRented(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return $property->agents()->where('agent_id', $user->id)->firstOrFail();
        }

        return $user->can('sold_out_update::property::status') && ($property->status == PropertyStatus::Posted || $property->status == PropertyStatus::SoldOut) && $property->is_rentable;
    }
}
