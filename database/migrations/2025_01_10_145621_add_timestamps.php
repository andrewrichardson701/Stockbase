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
        Schema::table('tag', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('manufacturer', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_comment', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_vendor', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_speed', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_distance', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_connector', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
        Schema::table('optic_type', function (Blueprint $table) {
            //
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config', function (Blueprint $table) {
            //
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
