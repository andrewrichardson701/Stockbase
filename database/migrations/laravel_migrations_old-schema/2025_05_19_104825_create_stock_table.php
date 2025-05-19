<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->text('name');
            $table->text('description');
            $table->text('sku');
            $table->integer('min_stock');
            $table->boolean('is_cable');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock');
    }
};
