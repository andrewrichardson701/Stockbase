<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cable_item', function (Blueprint $table) {
                        $table->integer('id');
            $table->integer('stock_id');
            $table->integer('quantity');
            $table->decimal('cost');
            $table->integer('shelf_id');
            $table->integer('type_id');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cable_item');
    }
};
