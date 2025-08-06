<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\Crypt;
use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Laravel\LdapRecord;

use Illuminate\Support\Facades\Log;

class LdapDynamicConfigProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            $settings = GeneralModel::config();

            if ($settings && $settings['ldap_enabled']) {
                $connection = new Connection([
                    'hosts' => [$settings['ldap_host'], $settings['ldap_host_secondary']],
                    'username' => $settings['ldap_username'],
                    'password' => base64_decode($settings['ldap_password']),
                    'base_dn' => $settings['ldap_basedn'],
                    'port' => $settings['ldap_port'],
                    'use_ssl' => false,
                    'use_tls' => false,
                    'timeout' => 5,
                ]);

                $container = Container::getInstance();
                $container->addConnection($connection, 'default');

                // Log::info('[LDAP] Dynamic connection registered.', [
                //     'hosts' => $connection->getConfiguration()->get('hosts'),
                //     'base_dn' => $connection->getConfiguration()->get('base_dn'),
                // ]);
                // OPTIONAL: Validate connection by doing a test search
                $connection->auth()->bind();

                // Build user search DN (e.g. "CN=Users,DC=domain,DC=com")
                $searchDn = $settings['ldap_usergroup'] . ',' . $settings['ldap_basedn'];

                // Apply raw filter — placeholder will be replaced later at login time
                $userFilter = $settings['ldap_userfilter'] ?? '(objectClass=user)';

                // Store somewhere globally accessible if needed, or just verify connection here.
                $query = $connection->query()
                    ->setDn($searchDn)
                    ->rawFilter($userFilter)
                    ->limit(1); // Just to validate query, not fetch everything

                $results = $query->get(['dn']);

                if (empty($results)) {
                    Log::warning('[LDAP] No entries matched initial user filter.');
                }

                // Log::info('[LDAP] Dynamic connection established.');
            } else {
                Log::warning('[LDAP] Skipped connection — settings not enabled or missing.');
            }
        } catch (\Throwable $e) {
            Log::error('[LDAP] Failed to set up connection: ' . $e->getMessage());
        }
    }
}
