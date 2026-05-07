<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disk_rpm', function (Blueprint $table) {
            $table->id(); // primary key, int, not null
            $table->text('name'); // not nullable by default
            $table->boolean('deleted')->default(0);
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disk_rpm');
    }
};
