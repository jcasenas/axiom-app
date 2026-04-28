<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->integer('setting_id')->autoIncrement();
            $table->string('setting_key', 80)->unique('uq_setting_key');
            $table->text('setting_value');
            $table->string('description', 150)->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('updated_by', 'fk_settings_updated_by')
                  ->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};