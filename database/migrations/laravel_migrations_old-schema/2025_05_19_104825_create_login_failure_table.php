<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('login_failure', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->text('username');
            $table->text('auth');
            $table->bigInteger('ipv4');
            // TODO: Review column 'ipv6' with type 'varbinary'
            $table->timestamp('last_timestamp');
            $table->integer('count');
        });
    }

    public function down(): void {
        Schema::dropIfExists('login_failure');
    }
};
