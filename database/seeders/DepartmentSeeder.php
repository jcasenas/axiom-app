<?php

// ─────────────────────────────────────────────
// database/seeders/DepartmentSeeder.php
// ─────────────────────────────────────────────
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['department_name' => 'BSIT', 'description' => 'Bachelor of Science in Information Technology'],
            ['department_name' => 'BSCS', 'description' => 'Bachelor of Science in Computer Science'],
            ['department_name' => 'BSED', 'description' => 'Bachelor of Science in Education'],
            ['department_name' => 'BSBA', 'description' => 'Bachelor of Science in Business Administration'],
            ['department_name' => 'BSMA', 'description' => 'Bachelor of Science in Mathematics'],
        ]);
    }
}