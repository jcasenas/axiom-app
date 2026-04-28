<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_formats', function (Blueprint $table) {
            $table->integer('format_id')->autoIncrement();
            $table->enum('format_type', ['PDF', 'EPUB', 'MOBI'])->unique('uq_format_type');
            $table->string('description', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_formats');
    }
};