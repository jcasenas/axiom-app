<?php

// ─────────────────────────────────────────────
// database/seeders/EbookFormatSeeder.php
// ─────────────────────────────────────────────
class EbookFormatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ebook_formats')->insert([
            ['format_type' => 'PDF',  'description' => 'Portable Document Format'],
            ['format_type' => 'EPUB', 'description' => 'Electronic Publication'],
            ['format_type' => 'MOBI', 'description' => 'Mobipocket eBook'],
        ]);
    }
}