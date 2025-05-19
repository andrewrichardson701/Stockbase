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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->text('auth')->nullable();
            $table->integer('role_id')->default(1);
            $table->integer('theme_id')->default(0);
            $table->text('2fa_secret')->nullable();
            $table->boolean('2fa_enabled')->default(false);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->boolean('password_expired')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
