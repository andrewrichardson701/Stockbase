<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
                        $table->integer('id');
            $table->text('username');
            $table->text('first_name');
            $table->text('last_name');
            $table->text('email');
            $table->text('auth');
            $table->text('password');
            $table->integer('role_id');
            $table->boolean('enabled');
            $table->boolean('password_expired');
            $table->integer('theme_id');
            $table->integer('card_primary');
            $table->integer('card_secondary');
            $table->text('2fa_secret');
            $table->boolean('2fa_enabled');
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
