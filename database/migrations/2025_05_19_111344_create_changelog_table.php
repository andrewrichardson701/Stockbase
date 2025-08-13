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
        Schema::create('changelog', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('timestamp');
            $table->integer('user_id');
            $table->text('user_username')->nullable();
            $table->string('action');
            $table->string('table_name')->nullable();
            $table->integer('record_id')->nullable();
            $table->string('field_name')->nullable();
            $table->text('value_old')->nullable();
            $table->text('value_new')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changelog');
    }
};
