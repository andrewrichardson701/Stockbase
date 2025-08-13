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
        Schema::create('session_log', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('user_id');
            $table->integer('login_time');
            $table->integer('logout_time')->nullable();
            $table->bigInteger('ipv4')->nullable();
            $table->binary('ipv6')->nullable();
            $table->text('browser');
            $table->text('os');
            $table->text('status');
            $table->integer('last_activity');
            $table->bigInteger('login_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_log');
    }
};
