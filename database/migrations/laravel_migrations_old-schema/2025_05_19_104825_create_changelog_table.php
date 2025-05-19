<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('changelog', function (Blueprint $table) {
                        $table->integer('id');
            $table->dateTime('timestamp');
            $table->integer('user_id');
            $table->text('user_username');
            $table->string('action', 255);
            $table->string('table_name', 255);
            $table->integer('record_id');
            $table->string('field_name', 255);
            $table->text('value_old');
            $table->text('value_new');
        });
    }

    public function down(): void {
        Schema::dropIfExists('changelog');
    }
};
