<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cable_types', function (Blueprint $table) {
                        $table->integer('id');
            $table->text('name');
            $table->text('description');
            $table->text('parent');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cable_types');
    }
};
