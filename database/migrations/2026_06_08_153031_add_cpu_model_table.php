<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpu_model', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('socket_id');
            $table->text('cpu_family')->nullable();
            $table->integer('core_count');
            $table->string('clock_speed');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpu_model');
    }
};