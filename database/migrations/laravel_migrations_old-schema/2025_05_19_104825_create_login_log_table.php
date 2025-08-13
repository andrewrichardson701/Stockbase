<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('login_log', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->text('type');
            $table->text('username');
            $table->integer('user_id');
            $table->bigInteger('ipv4');
            // TODO: Review column 'ipv6' with type 'varbinary'
            $table->timestamp('timestamp');
            $table->text('auth');
        });
    }

    public function down(): void {
        Schema::dropIfExists('login_log');
    }
};
