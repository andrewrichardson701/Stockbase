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
        Schema::table('config_default', function (Blueprint $table) {
            //
            $table->string('webhook_avatar_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_default', function (Blueprint $table) {
            //
            $table->dropColumn('webhook_avatar_url');
        });
    }
};
