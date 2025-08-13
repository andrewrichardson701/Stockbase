<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('password_reset', function (Blueprint $table) {
                        $table->integer('id');
            $table->integer('reset_user_id');
            $table->text('reset_selector');
            $table->text('reset_token');
            $table->text('reset_expires');
        });
    }

    public function down(): void {
        Schema::dropIfExists('password_reset');
    }
};
