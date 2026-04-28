<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebooks', function (Blueprint $table) {
            $table->integer('ebook_id')->autoIncrement();
            $table->integer('category_id');
            $table->integer('author_id');
            $table->integer('format_id');
            $table->string('title', 255);
            $table->string('isbn', 20)->nullable()->unique('uq_isbn');
            $table->year('published_year')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->text('file_url')->nullable();
            $table->text('cover_url')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'unavailable', 'archived'])->default('active');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('category_id', 'fk_ebooks_category')
                  ->references('category_id')->on('ebook_categories');
            $table->foreign('author_id', 'fk_ebooks_author')
                  ->references('author_id')->on('ebook_authors');
            $table->foreign('format_id', 'fk_ebooks_format')
                  ->references('format_id')->on('ebook_formats');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebooks');
    }
};