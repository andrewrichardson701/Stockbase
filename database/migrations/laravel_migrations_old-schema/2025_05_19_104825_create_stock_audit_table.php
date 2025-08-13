<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_audit', function (Blueprint $table) {
                        $table->bigInteger('id');
            $table->bigInteger('stock_id');
            $table->bigInteger('user_id');
            $table->date('date');
            $table->text('comment');
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_audit');
    }
};
