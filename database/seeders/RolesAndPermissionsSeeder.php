<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Role
        $roles = ['admin', 'developer', 'user'];
        foreach ($roles as $role) {
            DB::table('web_roles')->updateOrInsert(
                ['name' => ucfirst($role)],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Permisos
        $entities = ['orders', 'users', 'products'];
        $actions = ['view', 'create', 'update', 'delete'];
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                DB::table('web_permissions')->updateOrInsert(
                    [
                        'name' => ucfirst($action) . ' ' . ucfirst($entity),
                        'slug' => Str::slug($action . ' ' . $entity)
                    ],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        // Asignar todos los permisos al rol Admin
        $adminRole = DB::table('web_roles')->where('name', 'Admin')->first();
        $permissions = DB::table('web_permissions')->pluck('id');
        if ($adminRole) {
            foreach ($permissions as $permissionId) {
                DB::table('web_permission_role')->updateOrInsert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId
                ], []);
            }
        }
    }
}