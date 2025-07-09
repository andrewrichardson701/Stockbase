<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

}

