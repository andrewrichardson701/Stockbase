<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disk_vendor', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('disk_speed', function (Blueprint $table) {
            $table->id();
            $table->string('speed', 50)->unique();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('disk_capacity', function (Blueprint $table) {
            $table->id();
            $table->string('capacity', 50)->unique();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('disk_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('disk_caddy', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('disk_item', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('disk_vendor');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->string('form_factor', 50)->nullable();

            $table->foreignId('speed_id')->constrained('disk_speed');
            $table->foreignId('capacity_id')->constrained('disk_capacity');
            $table->foreignId('type_id')->constrained('disk_type');
            $table->foreignId('caddy_id')->constrained('disk_caddy');

            $table->boolean('ssd')->default(0);
            $table->unsignedBigInteger('shelf_id');

            $table->boolean('deleted')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('vendor_id');
            $table->index('speed_id');
            $table->index('capacity_id');
            $table->index('caddy_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disk_item');
        Schema::dropIfExists('disk_caddy');
        Schema::dropIfExists('disk_type');
        Schema::dropIfExists('disk_capacity');
        Schema::dropIfExists('disk_speed');
        Schema::dropIfExists('disk_vendor');
    }
};