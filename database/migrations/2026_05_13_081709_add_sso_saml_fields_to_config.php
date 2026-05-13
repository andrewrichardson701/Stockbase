<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('config', function (Blueprint $table) {
            // Adds a boolean column, non-nullable, defaulting to 0 (false)
            $table->boolean('saml_enabled')->default(false);

            // Adds text columns that allow null values
            $table->text('saml_tenant_id')->nullable();
            $table->text('saml_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config', function (Blueprint $table) {
            $table->dropColumn(['saml_enabled', 'saml_tenant_id', 'saml_settings']);
        });
    }
};