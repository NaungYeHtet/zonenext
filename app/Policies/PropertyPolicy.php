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
            return (bool) $user->leads()->where('property_id', $property->id)->first() ?? false;
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
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        return $user->can('update_property') && $property->status != PropertyStatus::Completed;
    }

    public function delete(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        return $user->can('delete_property') && ! $property->trashed();
    }

    public function restore(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        return $user->can('restore_property') && $property->trashed();
    }

    public function updatePosted(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        if ($user->hasRole('Agent')) {
            return false;
        }

        return $user->can('posted_update::property::status') && $property->status == PropertyStatus::Draft;
    }

    public function updateSoldOut(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        return $user->can('sold_out_update::property::status') && ($property->status == PropertyStatus::Posted || $property->status == PropertyStatus::Rented) && $property->is_saleable;
    }

    public function updateRented(Admin $user, Property $property): bool
    {
        if ($user->hasRole('Agent')) {
            return $user->leads()->where('property_id', $property->id)->first() ?? false;
        }

        return $user->can('sold_out_update::property::status') && ($property->status == PropertyStatus::Posted || $property->status == PropertyStatus::SoldOut) && $property->is_rentable;
    }
}
