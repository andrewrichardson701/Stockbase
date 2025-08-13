<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('config', function (Blueprint $table) {
                        $table->integer('id');
            $table->text('banner_color');
            $table->text('logo_image');
            $table->text('favicon_image');
            // TODO: Review column 'ldap_enabled' with type 'tinyint'
            $table->text('ldap_username');
            $table->text('ldap_password');
            $table->text('ldap_domain');
            $table->text('ldap_host');
            $table->integer('ldap_port');
            $table->text('ldap_basedn');
            $table->text('ldap_usergroup');
            $table->text('ldap_userfilter');
            $table->text('currency');
            $table->text('sku_prefix');
            $table->text('smtp_host');
            $table->integer('smtp_port');
            $table->text('smtp_encryption');
            $table->text('smtp_password');
            $table->text('smtp_from_email');
            $table->text('smtp_from_name');
            $table->text('smtp_to_email');
            $table->text('smtp_username');
            $table->text('system_name');
            $table->text('ldap_host_secondary');
            $table->text('base_url');
            $table->boolean('smtp_enabled');
            $table->integer('default_theme_id');
            $table->boolean('cost_enable_normal');
            $table->boolean('cost_enable_cable');
            $table->boolean('footer_enable');
            $table->boolean('footer_left_enable');
            $table->boolean('footer_right_enable');
            $table->boolean('2fa_enabled');
            $table->boolean('2fa_enforced');
            $table->boolean('signup_allowed');
        });
    }

    public function down(): void {
        Schema::dropIfExists('config');
    }
};
