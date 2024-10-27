<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_admin');
    }

    public function view(Admin $user): bool
    {
        return $user->can('view_admin');
    }

    public function create(Admin $user): bool
    {
        return $user->can('create_admin');
    }

    public function update(Admin $user, Admin $property): bool
    {
        return $user->can('update_admin');
    }

    public function delete(Admin $user, Admin $property): bool
    {
        return $user->can('delete_admin');
    }
}
