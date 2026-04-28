<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->integer('borrow_id')->autoIncrement();
            $table->integer('user_id');
            $table->integer('ebook_id');
            $table->integer('approved_by')->nullable();
            $table->date('borrow_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('access_url')->nullable();
            $table->dateTime('access_expires_at')->nullable();
            $table->enum('status', ['pending', 'active', 'due_soon', 'expired', 'cancelled'])->default('pending');
            $table->dateTime('requested_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_borrowings_user')
                  ->references('user_id')->on('users');
            $table->foreign('ebook_id', 'fk_borrowings_ebook')
                  ->references('ebook_id')->on('ebooks');
            $table->foreign('approved_by', 'fk_borrowings_approved_by')
                  ->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};