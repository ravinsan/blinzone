<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name'       => 'Super Admin',
                'guard_name' => 'API',
                'role_level' => 1,
                'status'     => 1,
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'name'       => 'Vendor',
                'guard_name' => 'API',
                'role_level' => 2,
                'status'     => 1,
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'name'       => 'Customer',
                'guard_name' => 'API',
                'role_level' => 3,
                'status'     => 1,
                'created_by' => null,
                'updated_by' => null,
            ],
        ]);
    }
}