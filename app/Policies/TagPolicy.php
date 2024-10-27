<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Tag;

class TagPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_tag');
    }

    public function view(Admin $user): bool
    {
        return $user->can('view_tag');
    }

    public function create(Admin $user): bool
    {
        return $user->can('create_tag');
    }

    public function update(Admin $user, Tag $tag): bool
    {
        return $user->can('update_tag');
    }

    public function delete(Admin $user, Tag $tag): bool
    {
        return $user->can('delete_tag');
    }
}
