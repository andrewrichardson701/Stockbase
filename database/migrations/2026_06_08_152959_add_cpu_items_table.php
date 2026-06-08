<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpu_item', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_id');
            $table->integer('model_id');
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('shelf_id'); 
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpu_item');
    }
};