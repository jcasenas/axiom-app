<?php

// ─────────────────────────────────────────────
// database/seeders/SystemSettingSeeder.php
// ─────────────────────────────────────────────
class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->insert([
            ['setting_key' => 'library_name',       'setting_value' => 'University of Mindanao Library', 'description' => 'Display name of the library',                              'updated_by' => null],
            ['setting_key' => 'borrow_window_days',  'setting_value' => '7',                              'description' => 'Default number of days a student can access a borrowed e-book', 'updated_by' => null],
            ['setting_key' => 'max_borrows_student', 'setting_value' => '3',                              'description' => 'Maximum concurrent borrows allowed for students',           'updated_by' => null],
            ['setting_key' => 'max_borrows_faculty', 'setting_value' => '5',                              'description' => 'Maximum concurrent borrows allowed for faculty',            'updated_by' => null],
            ['setting_key' => 'due_soon_threshold',  'setting_value' => '2',                              'description' => 'Number of days before expiry to mark a borrow as Due Soon', 'updated_by' => null],
            ['setting_key' => 'system_status',       'setting_value' => 'active',                         'description' => 'active or maintenance',                                    'updated_by' => null],
        ]);
    }
}