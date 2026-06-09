<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vendor (Corsair, Kingston, Crucial)
        Schema::create('memory_vendor', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        // Generation (DDR3, DDR4, DDR5)
        Schema::create('memory_generation', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false)->index();
            $table->timestamps();
        });

        // Capacity (8GB, 16GB, 32GB)
        Schema::create('memory_capacity', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false)->index();
            $table->timestamps();
        });

        // Speed (3200 MHz, 5600 MHz)
        Schema::create('memory_speed', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        // Form Factor (DIMM, SO-DIMM)
        Schema::create('memory_form_factor', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        // ECC Type (Non-ECC, ECC Registered)
        Schema::create('memory_ecc_type', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_ecc_type');
        Schema::dropIfExists('memory_form_factor');
        Schema::dropIfExists('memory_speed');
        Schema::dropIfExists('memory_capacity');
        Schema::dropIfExists('memory_generation');
        Schema::dropIfExists('memory_vendor');
    }
};