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
        Schema::table('config', function (Blueprint $table) {
            //
            $table->string('webhook_type')->nullable();
            $table->string('webhook_friendly_name')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('webhook_display_name')-> nullable();
            $table->string('webhook_prefix_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config', function (Blueprint $table) {
            //
            $table->dropColumn([
                'webhook_type',
                'webhook_friendly_name',
                'webhook_url',
                'webhook_display_name',
                'webhook_prefix_message',
            ]);
        });
    }
};
