<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('container', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->text('name');
            $table->text('description');
            $table->integer('shelf_id');
            $table->boolean('deleted');
        });
    }

    public function down(): void {
        Schema::dropIfExists('container');
    }
};
