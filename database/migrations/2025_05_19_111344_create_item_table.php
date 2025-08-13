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
        Schema::create('item', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('stock_id');
            $table->text('upc')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('cost', 10, 0)->nullable()->default(0);
            $table->text('serial_number')->nullable();
            $table->longText('comments')->nullable();
            $table->bigInteger('manufacturer_id')->nullable();
            $table->integer('shelf_id')->default(0);
            $table->boolean('is_container')->default(false);
            $table->boolean('deleted')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item');
    }
};
