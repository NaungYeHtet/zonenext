<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Tag;

class TagPolicy
{
    public function viewAny(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_tag');
    }

    public function view(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('view_tag');
    }

    public function create(Admin $user): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('create_tag');
    }

    public function update(Admin $user, Tag $tag): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('update_tag');
    }

    public function delete(Admin $user, Tag $tag): bool
    {
        if ($user->hasRole('Agent')) {
            return true;
        }

        return $user->can('delete_tag');
    }
}
