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
        Schema::create('cable_item', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('stock_id');
            $table->integer('quantity');
            $table->decimal('cost', 10, 0)->nullable();
            $table->integer('shelf_id')->default(0);
            $table->integer('type_id')->default(1);
            $table->boolean('deleted')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_item');
    }
};
