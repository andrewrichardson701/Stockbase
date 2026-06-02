<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('memory_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('table_name');
            $table->bigInteger('item_id');
            $table->text('type');
            $table->text('reason');
            $table->date('date');
            $table->time('time');
            $table->text('username');
            $table->bigInteger('shelf_id');
            $table->timestamps(); // created_at & updated_at (nullable)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_transaction');
    }
};