<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_old', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyText('username');
            $table->tinyText('first_name');
            $table->tinyText('last_name');
            $table->text('email');
            $table->tinyText('auth')->nullable();
            $table->longText('password')->nullable();
            $table->integer('role_id')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('password_expired')->default(false);
            $table->integer('theme_id')->nullable()->default(0);
            $table->integer('card_primary')->nullable();
            $table->integer('card_secondary')->nullable();
            $table->text('2fa_secret')->nullable();
            $table->boolean('2fa_enabled')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_old');
    }
};
