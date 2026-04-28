<?php
// ─────────────────────────────────────────────
// database/seeders/UserRoleSeeder.php
// ─────────────────────────────────────────────
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 
class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->insert([
            ['role_name' => 'Admin',     'borrow_limit' => 0, 'borrow_days' => 0],
            ['role_name' => 'Librarian', 'borrow_limit' => 0, 'borrow_days' => 0],
            ['role_name' => 'Faculty',   'borrow_limit' => 5, 'borrow_days' => 7],
            ['role_name' => 'Student',   'borrow_limit' => 3, 'borrow_days' => 7],
        ]);
    }
}