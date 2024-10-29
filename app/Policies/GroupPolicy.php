<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Group;

class GroupPolicy
{
    public function viewAny(Admin|Agent $user): bool
    {
        return $user->can('view_group');
    }

    public function view(Admin|Agent $user): bool
    {
        return $user->can('view_group');
    }

    public function create(Admin|Agent $user): bool
    {
        return $user->can('create_group');
    }

    public function update(Admin|Agent $user, Group $group): bool
    {
        return $user->can('update_group') && $group->updatable;
    }

    public function delete(Admin|Agent $user, Group $group): bool
    {
        return $user->can('delete_group');
    }
}
