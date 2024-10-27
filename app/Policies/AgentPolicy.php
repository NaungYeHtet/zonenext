<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Agent;

class AgentPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_agent');
    }

    public function view(Admin $user): bool
    {
        return $user->can('view_agent');
    }

    public function create(Admin $user): bool
    {
        return $user->can('create_agent');
    }

    public function update(Admin $user, Agent $property): bool
    {
        return $user->can('update_agent');
    }

    public function delete(Admin $user, Agent $property): bool
    {
        return $user->can('delete_agent');
    }
}
