<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('disk_capacity', function (Blueprint $table) {
            $table->renameColumn('capacity', 'name');
        });
    }

    public function down(): void
    {
        Schema::table('disk_capacity', function (Blueprint $table) {
            $table->renameColumn('name', 'capacity');
        });
    }
};