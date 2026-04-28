<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->integer('role_id')->autoIncrement();
            $table->string('role_name', 30)->unique('uq_role_name');
            $table->integer('borrow_limit')->default(3);
            $table->integer('borrow_days')->default(7);
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};