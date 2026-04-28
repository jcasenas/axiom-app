<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            DepartmentSeeder::class,
            EbookAuthorSeeder::class,
            EbookCategorySeeder::class,
            EbookFormatSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}