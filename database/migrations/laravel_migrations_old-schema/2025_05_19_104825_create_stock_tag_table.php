<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_tag', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->bigInteger('stock_id');
            $table->bigInteger('tag_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_tag');
    }
};
