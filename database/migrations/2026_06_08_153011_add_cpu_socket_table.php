<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpu_socket', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('deleted')->default(false);
            $table->timestamps(); // Handles created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpu_socket');
    }
};