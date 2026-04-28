<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get Admin role_id
        $adminRoleId = DB::table('user_roles')
            ->where('role_name', 'Admin')
            ->value('role_id');

        DB::table('users')->insertOrIgnore([
            'role_id'        => $adminRoleId,
            'department_id'  => null,
            'full_name'      => 'Admin',
            'email'          => 'admin@email.com',
            'password'       => Hash::make('password'),
            'account_status' => 'active',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}