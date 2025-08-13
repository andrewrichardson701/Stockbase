<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users_roles', function (Blueprint $table) {
                        $table->integer('id');
            $table->text('name');
            $table->text('description');
            $table->boolean('is_optic');
            $table->boolean('is_admin');
            $table->boolean('is_root');
        });
    }

    public function down(): void {
        Schema::dropIfExists('users_roles');
    }
};
