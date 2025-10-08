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
        //
        Schema::table('webhook_templates', function (Blueprint $table) {
            //
            $table->string('name', 100)->change();
            $table->string('slug', 100)->change();
            $table->text('description')->change();
            $table->text('variables')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('webhook_templates', function (Blueprint $table) {
            //
            $table->string('name', 255)->change();
            $table->string('slug', 255)->change();
            $table->string('description')->change();
            $table->string('variables')->change();
        });
    }
};
