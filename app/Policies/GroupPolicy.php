<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Group;

class GroupPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_group');
    }

    public function view(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_group');
    }

    public function create(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('create_group');
    }

    public function update(Admin $user, Group $group): bool
    {
        if ($user->hasRole('Agent')) {
            return $group->updatable;
        }

        return $user->can('update_group') && $group->updatable;
    }

    public function delete(Admin $user, Group $group): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('delete_group');
    }
}
