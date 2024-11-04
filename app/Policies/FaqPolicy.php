<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Faq;

class FaqPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_faq');
    }

    public function view(Admin $user, Faq $faq): bool
    {
        return $user->can('view_faq');
    }

    public function create(Admin $user): bool
    {
        return $user->can('create_faq');
    }

    public function update(Admin $user, Faq $faq): bool
    {
        return $user->can('update_faq');
    }

    public function delete(Admin $user, Faq $faq): bool
    {
        return $user->can('delete_faq');
    }
}
