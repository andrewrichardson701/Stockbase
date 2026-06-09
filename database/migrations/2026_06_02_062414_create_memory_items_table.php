<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memory_item', function (Blueprint $table) {
            $table->id();
            $table->string('model', 150); 
            $table->string('serial_number', 100)->nullable()->unique(); 
            
            // Foreign Keys
            $table->integer('generation_id');
            $table->integer('vendor_id')->constrained('memory_vendor');
            $table->integer('capacity_id')->constrained('memory_capacity');
            $table->integer('speed_id')->constrained('memory_speed');
            $table->integer('form_factor_id')->constrained('memory_form_factor');
            $table->integer('ecc_type_id')->constrained('memory_ecc_type');
            $table->integer('shelf_id')->constrained('shelf');
            
            // Custom Boolean Delete Flag
            $table->boolean('deleted')->default(false)->index(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_item');
    }
};