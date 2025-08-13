<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->bigInteger('stock_id');
            $table->text('upc');
            $table->integer('quantity');
            $table->decimal('cost');
            $table->text('serial_number');
            $table->text('comments');
            $table->bigInteger('manufacturer_id');
            $table->integer('shelf_id');
            $table->boolean('is_container');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('item');
    }
};
