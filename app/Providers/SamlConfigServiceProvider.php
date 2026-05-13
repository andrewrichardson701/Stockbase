<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SamlConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1. Check that both our custom table AND the package's table exist
        if (!app()->runningInConsole() && Schema::hasTable('configs') && Schema::hasTable('saml2_tenants')) {
            $dbConfig = DB::table('configs')->first();
            
            if ($dbConfig && $dbConfig->saml_enabled) {
                $tenantId = $dbConfig->saml_tenant_id;
                
                // ==========================================
                // 2. THE CHECKER: Ensure Dummy Row Exists
                // ==========================================
                $tenantExists = DB::table('saml2_tenants')->where('uuid', 'm365')->exists();
                
                if (!$tenantExists) {
                    // Insert a safe placeholder row to satisfy the package's internal database check
                    DB::table('saml2_tenants')->insert([
                        'uuid'         => 'm365',
                        'key'          => 'm365',
                        'idp_entityId' => 'placeholder',
                        'idp_loginUrl' => 'https://placeholder.local',
                        'idp_x509cert' => 'placeholder',
                        'metadata'     => json_encode([]), // Required to prevent JSON casting errors
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                // ==========================================
                // 3. DYNAMIC CONFIGURATION INJECTION
                // ==========================================
                
                // Parse the JSON settings from your configs table
                $settings = json_decode($dbConfig->saml_settings, true);
                $cert = $settings['idp_cert'] ?? '';

                $dynamicConfig = [
                    'idp' => [
                        'entityId' => "https://sts.windows.net/{$tenantId}/",
                        'singleSignOnService' => [
                            'url' => "https://login.microsoftonline.com/{$tenantId}/saml2",
                        ],
                        'x509cert' => $cert,
                    ],
                    'sp' => [
                        'entityId' => route('saml.metadata', ['uuid' => 'm365']),
                        'assertionConsumerService' => [
                            'url' => route('saml.acs', ['uuid' => 'm365']),
                        ],
                    ]
                ];
                
                // Inject into the package config
                Config::set('saml2.tenants.m365', $dynamicConfig);
            }
        }
    }
}