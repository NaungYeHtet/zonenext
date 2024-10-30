<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_project');
    }

    public function view(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_project');
    }

    public function create(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('create_project');
    }

    public function update(Admin $user, Project $project): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('update_project');
    }

    public function delete(Admin $user, Project $project): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('delete_project');
    }
}
