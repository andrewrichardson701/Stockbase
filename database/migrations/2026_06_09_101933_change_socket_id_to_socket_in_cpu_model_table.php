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
        Schema::table('cpu_model', function (Blueprint $table) {
            // 1. Drop the old integer column
            $table->dropColumn('socket_id');
            
            // 2. Add the new text column
            $table->text('socket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpu_model', function (Blueprint $table) {
            // To rollback, we reverse the process: drop 'socket' and add 'socket_id' back
            $table->dropColumn('socket');
            $table->integer('socket_id');
        });
    }
};
