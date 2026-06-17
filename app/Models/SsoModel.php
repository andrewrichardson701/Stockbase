<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;

use Illuminate\Support\Facades\DB;

class SsoModel extends Model
{
    //
    static public function toggleSso($enabled)
    {
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('error', 'Permission denied.');
        }

        if (in_array($enabled, ['on', 'off'])) {
            if ($enabled == 'on') {
                $enabled = 1;
            } else {
                $enabled = 0;
            }

            if (is_numeric($enabled)) {
                $current_data = DB::table('config')
                        ->select('saml_enabled')
                        ->where('id', 1)
                        ->first();

                if ($current_data) {
                    $previous_value = $current_data->saml_enabled;

                    $state = $enabled == 1 ? 'enabled' : 'disabled';

                    $update = DB::table('config')->where('id', 1)->update(['saml_enabled' => (int)$enabled, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => GeneralModel::getUser(),
                            'table' => 'config',
                            'record_id' => 1,
                            'action' => 'Update record',
                            'field' => 'saml_enabled',
                            'previous_value' => $previous_value,
                            'new_value' => (int)$enabled
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('success', 'SSO '.$state.'!');
                    } else {
                        return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('error', 'No changes made. Unable to toggle SSO');
                    }
                    
                } else {
                    return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('error', 'Unable to get current config.');
                }
            } else {
                return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('error', 'Invalid value.');
            }
        } else {
            return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('error', 'Invalid value.');
        }
    }
}
