<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('optic_distance', function (Blueprint $table) {
                        $table->integer('id');
            $table->text('name');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('optic_distance');
    }
};
