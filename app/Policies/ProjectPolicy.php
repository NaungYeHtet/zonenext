<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_project');
    }

    public function view(Admin $user): bool
    {
        return $user->can('view_project');
    }

    public function create(Admin $user): bool
    {
        return $user->can('create_project');
    }

    public function update(Admin $user, Project $project): bool
    {
        return $user->can('update_project');
    }

    public function delete(Admin $user, Project $project): bool
    {
        return $user->can('delete_project');
    }
}
