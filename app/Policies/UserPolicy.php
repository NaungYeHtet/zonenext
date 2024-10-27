<?php

namespace App\Policies;

use App\Models\Admin;

class UserPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_user');
    }

    public function view(Admin $user): bool
    {
        return $user->can('view_user');
    }
}
