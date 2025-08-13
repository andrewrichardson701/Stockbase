<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bypass_2fa', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->bigInteger('user_id');
            $table->text('cookie');
            $table->bigInteger('ipv4');
            // TODO: Review column 'ipv6' with type 'varbinary'
            $table->text('browser');
            $table->text('os');
            $table->timestamp('created_timestamp');
            $table->timestamp('expires_timestamp');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bypass_2fa');
    }
};
