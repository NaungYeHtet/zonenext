<?php

namespace App\Policies;

use App\Enums\PropertyStatus;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Property;

class PropertyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(Admin|Agent $user): bool
    {
        return $user instanceof Admin;
    }

    public function update(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return false;
        }

        return $property->status != PropertyStatus::Completed;
    }

    public function restore(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return false;
        }

        return $property->trashed();
    }

    public function updatePosted(Admin|Agent $user, Property $property): bool
    {
        if ($user instanceof Agent) {
            return false;
        }

        return $property->status == PropertyStatus::Draft;
    }

    public function updateSoldOut(Admin|Agent $user, Property $property): bool
    {
        return $property->status == PropertyStatus::Posted && $property->is_saleable;
    }

    public function updateRented(Admin|Agent $user, Property $property): bool
    {
        return $property->status == PropertyStatus::Posted && $property->is_rentable;
    }
}
