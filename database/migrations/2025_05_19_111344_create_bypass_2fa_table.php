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
        Schema::create('bypass_2fa', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->text('cookie');
            $table->bigInteger('ipv4')->nullable();
            $table->binary('ipv6')->nullable();
            $table->text('browser');
            $table->text('os');
            $table->timestamp('created_timestamp')->useCurrent();
            $table->timestamp('expires_timestamp');
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bypass_2fa');
    }
};
