<?php

namespace App\Policies;

use App\Models\Admin;

class UserPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_user');
    }

    public function view(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_user');
    }
}
