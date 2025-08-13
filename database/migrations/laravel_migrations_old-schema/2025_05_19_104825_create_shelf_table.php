<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shelf', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->text('name');
            $table->bigInteger('area_id');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('shelf');
    }
};
