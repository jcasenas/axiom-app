<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_categories', function (Blueprint $table) {
            $table->integer('category_id')->autoIncrement();
            $table->string('category_name', 80)->unique('uq_category_name');
            $table->text('description')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_categories');
    }
};