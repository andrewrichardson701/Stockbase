<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('users_permissions', function (Blueprint $table) {
            $table->integer('id')->primary()->nullable(false); // Regular int, not auto-increment

            // Boolean permission columns, defaulting to false (0)
            $table->boolean('root')->default(false);
            $table->boolean('admin')->default(false);
            $table->boolean('locations')->default(false);
            $table->boolean('stock')->default(true);
            $table->boolean('cables')->default(false);
            $table->boolean('optics')->default(false);
            $table->boolean('cpus')->default(false);
            $table->boolean('memory')->default(false);
            $table->boolean('disks')->default(false);
            $table->boolean('fans')->default(false);
            $table->boolean('psus')->default(false);
            $table->boolean('containers')->default(false);
            $table->boolean('changelog')->default(false);

            // Timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_permissions');
    }
}
