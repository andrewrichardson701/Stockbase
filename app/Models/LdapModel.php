<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use LdapRecord\Connection;
use LdapRecord\Container;
use LdapRecord\Auth\BindException;
use Illuminate\Support\Facades\Log;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Query\Model\Builder;


/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LdapModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LdapModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LdapModel query()
 * @mixin \Eloquent
 */
class LdapModel extends Model

{
    //

    static public function toggleLdap($enabled)
    {
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('error', 'Permission denied.');
        }

        if (in_array($enabled, ['on', 'off'])) {
            if ($enabled == 'on') {
                $enabled = 1;
            } else {
                $enabled = 0;
            }

            if (is_numeric($enabled)) {
                $current_data = DB::table('config')
                        ->select('ldap_enabled')
                        ->where('id', 1)
                        ->first();

                if ($current_data) {
                    $previous_value = $current_data->ldap_enabled;

                    $state = $enabled == 1 ? 'enabled' : 'disabled';

                    $update = DB::table('config')->where('id', 1)->update(['ldap_enabled' => (int)$enabled, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => GeneralModel::getUser(),
                            'table' => 'config',
                            'record_id' => 1,
                            'action' => 'Update record',
                            'field' => 'ldap_enabled',
                            'previous_value' => $previous_value,
                            'new_value' => (int)$enabled
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('success', 'LDAP '.$state.'!');
                    } else {
                        return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('error', 'No changes made. Unable to toggle LDAP');
                    }
                    
                } else {
                    return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('error', 'Unable to get current config.');
                }
            } else {
                return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('error', 'Invalid value.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'ldap-settings']) . '#ldap-settings')->with('error', 'Invalid value.');
        }
    }

    protected Connection $ldapConnection;

    static public function ldapTest(string $check, string $ldap_username, string $ldap_password, string $ldap_domain, string $ldap_host, int $ldap_port, string $ldap_basedn, string $ldap_usergroup, string $ldap_userfilter): array 
    {
        $ldap_userlist = [];
        $errors = [];

        // Add check title and empty line
        $ldap_userlist[] = $check;
        $ldap_userlist[] = '';

        // Setup connection config
        $config = [
            'hosts'    => [$ldap_host],
            'port'     => $ldap_port,
            'base_dn'  => $ldap_basedn,
            'username' => $ldap_username,
            'password' => $ldap_password,
            'use_ssl'  => false,
            'use_tls'  => false,
        ];

        try {
            $connection = new Connection($config);

            // Add connection to container
            $container = Container::getInstance();
            $container->addConnection($connection, 'default');

            // Bind connection
            $connection->auth()->bind();

            $searchDn = $ldap_usergroup . ',' . $ldap_basedn;

            // dd($ldap_userfilter);
            // Perform LDAP query with raw filter
            $query = $connection->query()
                ->setDn($searchDn)
                ->rawFilter((string)$ldap_userfilter);

            $ldap_info = $query->get(['dn']);
            // dd(vars: $ldap_info);
            if (empty($ldap_info)) {
                $errors[] = "Error: Could not get entries from LDAP server: $ldap_host.";
            } else {
                foreach ($ldap_info as $entry) {
                    if (isset($entry['dn'])) {
                        $ldap_userlist[] = $entry['dn'];
                    }
                }

                $ldap_userlist[] = '';
                $ldap_userlist[] = "Count: " . count($ldap_info);
                $ldap_userlist[] = "======================================================";
                $ldap_userlist[] = '';
            }
        } catch (\Exception $e) {
            $errors[] = "Error: LDAP query failed on host $ldap_host: " . $e->getMessage();
        }
        

        // Append errors if any
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $ldap_userlist[] = $error;
            }
        }

        return $ldap_userlist;
    }

}
