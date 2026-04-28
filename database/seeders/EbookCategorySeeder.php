<?php

// ─────────────────────────────────────────────
// database/seeders/EbookCategorySeeder.php
// ─────────────────────────────────────────────
class EbookCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ebook_categories')->insert([
            ['category_name' => 'Fiction',    'description' => 'Literary fiction and novels'],
            ['category_name' => 'Academic',   'description' => 'Textbooks and academic references'],
            ['category_name' => 'Science',    'description' => 'Natural and applied sciences'],
            ['category_name' => 'History',    'description' => 'Historical accounts and references'],
            ['category_name' => 'Technology', 'description' => 'Computing, engineering, and IT'],
        ]);
    }
}