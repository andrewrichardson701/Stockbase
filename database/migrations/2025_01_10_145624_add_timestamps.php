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
        Schema::table('site', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('area', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('shelf', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site', function (Blueprint $table) {
            //
            $table->dropColumn(['created_at', 'updated_at']);
        });
        Schema::table('area', function (Blueprint $table) {
            //
            $table->dropColumn(['created_at', 'updated_at']);
        });
        Schema::table('shelf', function (Blueprint $table) {
            //
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
