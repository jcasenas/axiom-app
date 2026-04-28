<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_authors', function (Blueprint $table) {
            $table->integer('author_id')->autoIncrement();
            $table->string('author_name', 120);
            $table->text('bio')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_authors');
    }
};