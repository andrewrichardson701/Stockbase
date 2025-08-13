<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('session_log', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->integer('user_id');
            $table->integer('login_time');
            $table->integer('logout_time');
            $table->bigInteger('ipv4');
            // TODO: Review column 'ipv6' with type 'varbinary'
            $table->text('browser');
            $table->text('os');
            $table->text('status');
            $table->integer('last_activity');
            $table->bigInteger('login_log_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('session_log');
    }
};
