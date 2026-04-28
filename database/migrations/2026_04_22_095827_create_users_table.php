<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id')->autoIncrement();
            $table->integer('role_id');
            $table->integer('department_id')->nullable();
            $table->string('full_name', 100);
            $table->string('email', 100)->unique('uq_email');
            $table->string('password', 255);
            $table->string('profile_photo', 255)->nullable();
            $table->enum('account_status', ['pending', 'active', 'inactive'])->default('pending');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('last_login_at')->nullable();

            $table->foreign('role_id', 'fk_users_role')
                  ->references('role_id')->on('user_roles');
            $table->foreign('department_id', 'fk_users_department')
                  ->references('department_id')->on('departments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};