<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('disk_speed', function (Blueprint $table) {
            $table->renameColumn('speed', 'name');
        });
    }

    public function down(): void
    {
        Schema::table('disk_speed', function (Blueprint $table) {
            $table->renameColumn('name', 'speed');
        });
    }
};