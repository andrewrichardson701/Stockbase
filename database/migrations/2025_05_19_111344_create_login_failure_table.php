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
        Schema::create('login_failure', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('username');
            $table->text('auth')->nullable();
            $table->bigInteger('ipv4')->nullable();
            $table->binary('ipv6')->nullable();
            $table->timestamp('last_timestamp')->comment('Last failed attempet');
            $table->integer('count')->comment('Count of failures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_failure');
    }
};
