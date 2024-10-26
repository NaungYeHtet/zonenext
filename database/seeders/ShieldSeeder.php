<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = File::get(base_path('database/seeders/data/shield.json'));
        // $directPermissions = File::get(base_path('database/seeders/data/shield-direct-permissions.json'));
        // $directPermissions = '[]';

        static::makeRolesWithPermissions($permissions);
        static::makeDirectPermissions($permissions);
    }

    protected static function makeRolesWithPermissions(string $permissions): void
    {
        if (! blank($permissions = json_decode($permissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $rolePlusPermission) {
                if ($rolePlusPermission['direct']) {
                    continue;
                }

                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                $syncPermissions = new Collection;

                foreach ($rolePlusPermission['permissions'] as $groupName => $groupPermissions) {
                    if (count($groupPermissions)) {
                        $permissionModels = collect($groupPermissions)
                            ->map(function ($permission) use ($permissionModel, $rolePlusPermission, $groupName) {
                                return $permissionModel::firstOrCreate([
                                    'name' => $permission,
                                    'guard_name' => $rolePlusPermission['guard_name'],
                                    'group_name' => $groupName,
                                ]);
                            })
                            ->all();

                        $syncPermissions->push($permissionModels);
                    }
                }

                $role->syncPermissions($syncPermissions);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($guardPlusPermissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($guardPlusPermissions as $guardPlusPermission) {
                if (! $guardPlusPermission['direct']) {
                    continue;
                }

                foreach ($guardPlusPermission['permissions'] as $groupName => $permissionGroup) {
                    foreach ($permissionGroup as $permission) {
                        if ($permissionModel::whereName($permission)->whereGuardName($guardPlusPermission['guard_name'])->doesntExist()) {
                            $permissionModel::create([
                                'name' => $permission,
                                'guard_name' => $guardPlusPermission['guard_name'],
                                'group_name' => $groupName,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
