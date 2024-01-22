<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;
use App\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/role_permissions.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $role_name = trim($col[1]);
            $permission = trim($col[2]);

            if (empty($role_name) || empty($permission)) {
                continue;
            }

            $role = Role::where('name', $role_name)->first();
            if (empty($role)) {
                continue;
            }

            $exists = RolePermission::where('role_id', $role->id)->where('permission', $permission)->exists();
            if (!$exists) {
                $d = new RolePermission();
                $d->role_id = $role->id;
                $d->permission = $permission;
                $d->save();
            }
        }
        fclose($handle);

        $role_admin_name = 'พนักงาน System Admin';
        $role_admin = Role::where('name', $role_admin_name)->first();
        if ($role_admin) {
            $role_permissions = RolePermission::get();
            foreach ($role_permissions as $role_permission) {
                $exists = RolePermission::where('role_id', $role_admin->id)->where('permission', $role_permission->permission)->exists();
                if (!$exists) {
                    $d = new RolePermission();
                    $d->role_id = $role_admin->id;
                    $d->permission = $role_permission->permission;
                    $d->save();
                }
            }

            $handle = fopen(storage_path('init/resources.csv'), "r");
            $header = true;
            while ($col = fgetcsv($handle, 5000, ",")) {
                if ($header) {
                    $header = false;
                    continue;
                }
                if (sizeof($col) < 1) {
                    continue;
                }
                $resource = trim($col[0]);

                if (empty($resource)) {
                    continue;
                }

                $permission_manage = 'manage_' . strtolower(trim($resource));
                $permission_view = 'view_' . strtolower(trim($resource));

                $exists = RolePermission::where('role_id', $role_admin->id)->where('permission', $permission_manage)->exists();
                if (!$exists) {
                    $d = new RolePermission();
                    $d->role_id = $role_admin->id;
                    $d->permission = $permission_manage;
                    $d->save();
                }

                $exists2 = RolePermission::where('role_id', $role_admin->id)->where('permission', $permission_view)->exists();
                if (!$exists2) {
                    $d = new RolePermission();
                    $d->role_id = $role_admin->id;
                    $d->permission = $permission_view;
                    $d->save();
                }
            }
            fclose($handle);
        }
    }
}
