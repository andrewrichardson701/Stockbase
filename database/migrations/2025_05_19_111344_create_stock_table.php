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
        Schema::create('stock', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('name')->fulltext('name');
            $table->longText('description')->nullable()->fulltext('description');
            $table->text('sku');
            $table->integer('min_stock')->nullable()->default(0);
            $table->boolean('is_cable')->default(false);
            $table->boolean('deleted')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
