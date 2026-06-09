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
        Schema::table('users_permissions', function (Blueprint $table) {
            $table->dropColumn(['psus', 'fans']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_permissions', function (Blueprint $table) {
            // NOTE: Change 'string' to match the original data types of these columns
            $table->boolean('psus')->default(false);
            $table->boolean('fans')->default(false);
        });
    }
};