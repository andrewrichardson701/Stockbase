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
        Schema::create('login_log', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('type')->comment('login / logout / fail');
            $table->text('username');
            $table->integer('user_id')->nullable()->comment('Can be blank if the user doesnt match anything');
            $table->bigInteger('ipv4')->nullable();
            $table->binary('ipv6')->nullable();
            $table->timestamp('timestamp')->useCurrentOnUpdate();
            $table->text('auth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_log');
    }
};
