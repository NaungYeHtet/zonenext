<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     */
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_role');
    }

    /**
     * Determine whether the admin can view the model.
     */
    public function view(Admin $user, Role $role): bool
    {
        return $user->can('view_role');
    }

    /**
     * Determine whether the admin can create models.
     */
    public function create(Admin $user): bool
    {
        return $user->can('create_role');
    }

    /**
     * Determine whether the admin can update the model.
     */
    public function update(Admin $user, Role $role): bool
    {
        return $user->can('update_role');
    }

    /**
     * Determine whether the admin can delete the model.
     */
    public function delete(Admin $user, Role $role): bool
    {
        return $user->can('delete_role');
    }

    /**
     * Determine whether the admin can bulk delete.
     */
    public function deleteAny(Admin $user): bool
    {
        return $user->can('delete_role');
    }

    /**
     * Determine whether the admin can permanently delete.
     */
    public function forceDelete(Admin $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     */
    public function forceDeleteAny(Admin $user): bool
    {
        return false;
    }

    /**
     * Determine whether the admin can restore.
     */
    public function restore(Admin $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the admin can bulk restore.
     */
    public function restoreAny(Admin $user): bool
    {
        return false;
    }

    /**
     * Determine whether the admin can replicate.
     */
    public function replicate(Admin $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the admin can reorder.
     */
    public function reorder(Admin $user): bool
    {
        return false;
    }
}
