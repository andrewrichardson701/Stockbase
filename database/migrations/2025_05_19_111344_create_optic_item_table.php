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
        Schema::create('optic_item', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('model');
            $table->integer('vendor_id');
            $table->text('serial_number');
            $table->integer('type_id');
            $table->integer('connector_id');
            $table->tinyText('mode');
            $table->text('spectrum');
            $table->integer('speed_id');
            $table->integer('distance_id');
            $table->integer('site_id');
            $table->integer('quantity')->default(1);
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optic_item');
    }
};
