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
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('stock_id');
            $table->bigInteger('item_id');
            $table->text('type');
            $table->integer('quantity');
            $table->decimal('price', 10, 0)->nullable();
            $table->text('serial_number')->nullable();
            $table->text('reason');
            $table->longText('comments')->nullable();
            $table->date('date');
            $table->time('time');
            $table->text('username');
            $table->integer('shelf_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
