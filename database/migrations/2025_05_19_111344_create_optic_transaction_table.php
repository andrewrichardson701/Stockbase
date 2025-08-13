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
        Schema::create('optic_transaction', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('table_name');
            $table->bigInteger('item_id');
            $table->text('type');
            $table->text('reason');
            $table->date('date');
            $table->time('time');
            $table->text('username');
            $table->bigInteger('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optic_transaction');
    }
};
