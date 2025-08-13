<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_container', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->bigInteger('item_id');
            $table->integer('container_id');
            $table->boolean('container_is_item');
        });
    }

    public function down(): void {
        Schema::dropIfExists('item_container');
    }
};
