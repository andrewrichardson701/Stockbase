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
        Schema::table('config_default', function (Blueprint $table) {
            // Drop the column since we now use saml2_tenants
            $table->dropColumn('saml_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_default', function (Blueprint $table) {
            // Add it back as nullable json/text in case of a rollback
            $table->json('saml_settings')->nullable()->after('saml_enabled');
        });
    }
};