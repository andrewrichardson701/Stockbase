<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('api_keys', function (Blueprint $table) {
                        $table->integer('id');
            $table->integer('user_id');
            $table->text('secret');
            $table->timestamp('created');
            $table->timestamp('expiry');
            $table->boolean('enabled');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('api_keys');
    }
};
