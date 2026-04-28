<?php

// ─────────────────────────────────────────────
// database/seeders/EbookAuthorSeeder.php
// ─────────────────────────────────────────────
class EbookAuthorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ebook_authors')->insert([
            ['author_name' => 'Caroline Forgeat', 'bio' => null],
        ]);
    }
}