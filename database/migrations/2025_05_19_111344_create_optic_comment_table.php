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
        Schema::create('optic_comment', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('item_id');
            $table->text('comment');
            $table->integer('user_id');
            $table->dateTime('timestamp');
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optic_comment');
    }
};
