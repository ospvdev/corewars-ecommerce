<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('web_permission_role')->truncate();
        DB::table('web_role_user')->truncate();
        DB::table('web_permissions')->truncate();
        DB::table('web_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}