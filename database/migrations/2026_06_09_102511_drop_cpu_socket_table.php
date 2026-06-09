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
        Schema::dropIfExists('cpu_socket');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table exactly as it was if rolled back
        Schema::create('cpu_socket', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }
};