<?php

namespace App\Policies;

use App\Enums\PropertyStatus;
use App\Models\Admin;
use App\Models\Property;

class PropertyPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_property');
    }

    public function view(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $property->leads()->where('admin_id', $user->id)->exists();
        }

        return $user->can('view_property');
    }

    public function create(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('create_property');
    }

    public function update(Admin $user, Property $property): bool
    {
        if ($property->status != PropertyStatus::Completed) {
            return false;
        }

        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->exists();
        }

        return $user->can('update_property');
    }

    public function delete(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->exists();
        }

        return $user->can('delete_property') && ! $property->trashed();
    }

    public function restore(Admin $user, Property $property): bool
    {
        if (! $property->trashed()) {
            return false;
        }

        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->exists();
        }

        return $user->can('restore_property');
    }

    public function updatePosted(Admin $user, Property $property): bool
    {
        if ($property->status != PropertyStatus::Draft) {
            return false;
        }

        return $user->hasRole('Agent') && $user->leads()->where('property_id', $property->id)->exists();
    }

    public function updateUnposted(Admin $user, Property $property): bool
    {
        if ($property->status != PropertyStatus::Posted) {
            return false;
        }

        return $user->hasRole('Agent') && $user->leads()->where('property_id', $property->id)->exists();
    }
}
