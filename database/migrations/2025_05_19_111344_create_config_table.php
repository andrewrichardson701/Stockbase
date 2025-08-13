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
        Schema::create('config', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('banner_color')->nullable();
            $table->text('logo_image')->nullable();
            $table->text('favicon_image')->nullable();
            $table->tinyInteger('ldap_enabled')->nullable();
            $table->text('ldap_username')->nullable();
            $table->longText('ldap_password')->nullable();
            $table->text('ldap_domain')->nullable();
            $table->text('ldap_host')->nullable();
            $table->integer('ldap_port')->nullable();
            $table->text('ldap_basedn')->nullable();
            $table->text('ldap_usergroup')->nullable();
            $table->text('ldap_userfilter')->nullable();
            $table->text('currency')->nullable();
            $table->text('sku_prefix')->nullable();
            $table->text('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->text('smtp_encryption')->nullable();
            $table->longText('smtp_password')->nullable();
            $table->text('smtp_from_email')->nullable();
            $table->text('smtp_from_name')->nullable();
            $table->text('smtp_to_email')->nullable();
            $table->longText('smtp_username')->nullable();
            $table->text('system_name')->nullable();
            $table->text('ldap_host_secondary')->nullable();
            $table->text('base_url')->nullable();
            $table->boolean('smtp_enabled')->nullable()->default(false);
            $table->integer('default_theme_id')->default(1);
            $table->boolean('cost_enable_normal')->default(true);
            $table->boolean('cost_enable_cable')->default(true);
            $table->boolean('footer_enable')->default(true);
            $table->boolean('footer_left_enable')->default(true);
            $table->boolean('footer_right_enable')->default(true);
            $table->boolean('2fa_enabled')->default(false);
            $table->boolean('2fa_enforced')->default(false);
            $table->boolean('signup_allowed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
