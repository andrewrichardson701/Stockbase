<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\TransactionModel;


/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel query()
 * @mixin \Eloquent
 */
class AdminModel extends Model
{
    //
    static public function getActiveSessionLog()
    {
        $instance = new self();
        $instance->setTable('session_log AS sl');
                        
        return $instance->selectRaw('sl.id AS id, sl.sessions_id as sl_sessions_id, sl.user_id AS sl_user_id, 
                                    FROM_UNIXTIME(sl.login_time) AS sl_login_time, IFNULL(FROM_UNIXTIME(sl.logout_time), NULL) AS sl_logout_time, 
                                    sl.ip_address AS sl_ip,
                                    sl.browser AS sl_browser, sl.os AS sl_os, sl.status AS sl_status,
                                    FROM_UNIXTIME(sl.last_activity) AS sl_last_activity,
                                    u.username AS u_username')
                        ->join('users AS u', 'u.id', '=', 'sl.user_id')
                        ->where('sl.logout_time', '=', NULL)
                        ->get()
                        ->toarray();
    }

    static public function imageManagementCount()
    {
        $files = array_values(array_diff(scandir('img/stock'), array('..', '.'))); 
        $count = count($files);

        return $count;
    }

    static public function taggedStockByTagId() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result['tag_id']]['count']) || $return[$result['tag_id']]['count'] == 0) {
                $return[$result['tag_id']]['count'] = 0;
            }
            $return[$result['tag_id']]['count']++;
            $return[$result['tag_id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function taggedStockByStockId() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

    
        foreach($results as $result) {
            if (!isset($return[$result['stock_id']]['count']) || $return[$result['stock_id']]['count'] !== 0) {
                $return[$result['stock_id']]['count'] = 0;
            }
            $return[$result['stock_id']]['count']++;
            $return[$result['stock_id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function taggedStockById() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result['id']]['count']) || $return[$result['id']]['count'] !== 0) {
                $return[$result['id']]['count'] = 0;
            }
            $return[$result['id']]['count']++;
            $return[$result['id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function attributeLinks($search_table, $asset_field, $select=null, $deleted_count=null, $where=[])
    {
        $return = [];

        $instance = new self();
        $instance->setTable($search_table);

        $results = $instance->when($select !== null, function ($query) use ($select) {
                                $query->selectRaw($select);
                            })
                        ->when(!empty($where), function ($query) use ($where) {
                            foreach($where as $field => $value) {
                                $query->where($field, $value);
                            }
                        })
                        ->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result[$asset_field]]['count']) || $return[$result[$asset_field]]['count'] == 0) {
                $return[$result[$asset_field]]['count'] = 0;
            }
            
            

            if ($deleted_count == 1) {
                if (isset($result['deleted'])) {
                    if (!isset($return[$result[$asset_field]]['deleted_count']) || $return[$result[$asset_field]]['deleted_count'] !== 0) {
                        $return[$result[$asset_field]]['deleted_count'] = 0;
                    }
                    if ($result['deleted'] == 1) {
                        $return[$result[$asset_field]]['deleted_count']++;
                    } else {
                        $return[$result[$asset_field]]['count']++;
                    }
                } else {
                    $return[$result[$asset_field]]['count']++;
                }
            } else {
                $return[$result[$asset_field]]['count']++;
            }

            $return[$result[$asset_field]]['rows'][] = $result;
        }

        return $return;
    }

    static public function santizeFileName($file)
    {
        $original = $file->getClientOriginalName();
        $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $filename = $safeBase . '-' . $timestamp . '-' . uniqid() . '.' . $extension;
        // test_file-20250606083312-6661e5c1b3a1e.png
        // [name]-[timestamp]-[uniqid].[extension]

        return $filename;
    }

    static public function imageUpload($request, $field)
    {
        // dd($request[$field]->getClientOriginalName());
        $file = $request[$field];
    
        // Create a unique filename
        $filename = AdminModel::santizeFileName($file);

        // Move to public/img/stock
        $destinationPath = public_path('img/config/custom');
        // dd($destinationPath);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true); // 0755 permissions and recursive creation
        }
        $moved = $file->move($destinationPath, $filename);
    
        if ($moved) {
            // get current info to compare
            $current_data = DB::table('config')
                            ->where('id', 1)
                            ->first();
            // Save to DB
            $updated = DB::table('config')->where('id', 1)->update([$field => 'custom/'.$filename, 'updated_at' => now()]);
            if ($updated) {
                $user = GeneralModel::getUser();
                $info = [
                    'user' => $user,
                    'table' => 'config',
                    'record_id' => 1,
                    'field' => 'image',
                    'new_value' => $filename,
                    'action' => 'Update record',
                    'previous_value' => $current_data->$field,
                ];
                GeneralModel::updateChangelog($info);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function resetConfig($fields) 
    {
        $current_data = DB::table('config')
                            ->where('id', 1)
                            ->first();

        $default_data = DB::table('config_default')
                            ->select($fields)
                            ->where('id', 1)
                            ->first();

        if ($default_data) {
            $update = DB::table('config')->where('id', 1)->update((array)$default_data);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => GeneralModel::getUser(),
                    'table' => 'config',
                    'record_id' => 1,
                    'action' => 'Update record',
                ];
                foreach($default_data as $key => $data) {
                    $changelog_info['field'] = $key;
                    $changelog_info['previous_value'] = $current_data->$key;
                    $changelog_info['new_value'] = $data;

                    if ($current_data->$key != $data) {
                        GeneralModel::updateChangelog($changelog_info);
                    }
                }

                return 1;
            } 
        }

        return 0;
    }

    static public function updateConfigSettings($data)
    {
        $errors = [];
        $unchanged = [];
        $changed = [];

        $user = GeneralModel::getUser();
        $config_fields = Schema::getColumnListing('config');
        $excluded_keys = ['_token', 'global-submit', 'smtp-submit', 'ldap-submit'];

        if (isset($data['global-submit'])) {
            $anchor = 'global-settings';
        } elseif (isset($data['smtp-submit']) || isset($data['smtp-restore-defaults'])) {
            $anchor = 'smtp-settings';
        } elseif (isset($data['ldap-submit']) || isset($data['ldap-restore-defaults'])) {
            $anchor = 'ldap-settings';
        } else {
            $anchor = '';
        }

        $current_data = DB::table('config')
                            ->where('id', 1)
                            ->first();

        if (isset($data['global-restore-defaults'])) {
            $reset_array = ['system_name', 'banner_color', 'logo_image', 'favicon_image', 
                            'currency', 'sku_prefix', 'base_url', 'default_theme_id'];
            $reset = AdminModel::resetConfig($reset_array);

            if ($reset == 1) {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Config Reset.');
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Reset failed.');
            }
        } 

        if (isset($data['smtp-restore-defaults'])) {
        
            $reset_array = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username',
                            'smtp_password', 'smtp_from_email', 'smtp_from_name', 'smtp_to_email'];
            $reset = AdminModel::resetConfig($reset_array);

            if ($reset == 1) {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Config Reset.');
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Reset failed.');
            }
        } 

        if (isset($data['ldap-restore-defaults'])) {
        
            $reset_array = ['ldap_username', 'ldap_password', 'ldap_domain', 'ldap_host', 'ldap_host_secondary', 
                            'ldap_port', 'ldap_basedn', 'ldap_usergroup', 'ldap_userfilter'];
            $reset = AdminModel::resetConfig($reset_array);

            if ($reset == 1) {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Config Reset.');
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Reset failed.');
            }
        } 

        $changelog_info = [
                'user' => $user,
                'table' => 'config',
                'record_id' => 1,
                'action' => 'Update record',
            ];

        unset($data['_token'], $data['global-submit'], $data['smtp-submit'], $data['ldap-submit']); // remove these to stop them being queried

        foreach($data as $field => $value) {
            if (!in_array($field, $excluded_keys)) { // to stop the _token and submit keys
                if (!in_array($field, $config_fields)) {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unknown table field: '.$field.'.');
                }
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'Excluded field: '.$field.'.');
            }
        }
        
        foreach($data as $field => $value) {
            if (!in_array($field, ['favicon_image', 'logo_image'])) {
                // not an image 
                if (ctype_digit($value)) { // checks for numbers and NOT decimals
                    $value = (int)$value;
                }

                if (str_contains($field, 'password')) {
                    if ($value == 'password' || $value == '' || $value == null) {
                        $current_data->$field;
                    } else {
                        if (str_contains($field, 'smtp') || str_contains($field, 'ldap')) {
                            $value = base64_encode($value);
                        } else {
                            $value = Hash::make($value);
                        }
                    }
                    
                }

                if ($current_data->$field !== $value) {
                    //update needed
                    if (filled($value)) {

                        $update = DB::table('config')->where('id', 1)->update([$field => $value, 'updated_at' => now()]);
                        
                        if ($update) {
                            // changelog needed
                            $changelog_info['field'] = $field;
                            $changelog_info['previous_value'] = $current_data->$field;
                            $changelog_info['new_value'] = $value;

                            $changed[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'updated'];

                            GeneralModel::updateChangelog($changelog_info);
                        } else {
                            // failed to update DB
                            $errors[$field] = 'failed to update';
                            $unchanged[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'unable to push data to DB'];
                        }
                    } else {
                        $unchanged[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'null data'];
                    }
                } else {
                    $unchanged[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'matching data'];
                }
            }

            if (isset($data['favicon_image'])) {
                if (AdminModel::imageUpload($data, 'favicon_image') == 1) {
                    $changed[$field] = ['current' => $current_data->favicon_image, 'new_data' => $data['favicon_image']->getClientOriginalName(), 'reason' => 'updated'];
                } else {
                    $errors[$field] = 'image upload failed';
                    $unchanged[$field] = ['current' => $current_data->favicon_image, 'new_data' => $data['favicon_image']->getClientOriginalName(), 'reason' => 'unable to push data to DB'];
                }
            }

            if (isset($data['logo_image'])) {
                if (AdminModel::imageUpload($data, 'logo_image') == 1) {
                    $changed[$field] = ['current' => $current_data->logo_image, 'new_data' => $data['logo_image']->getClientOriginalName(), 'reason' => 'updated'];
                } else {
                    $errors[$field] = 'image upload failed';
                    $unchanged[$field] = ['current' => $current_data->logo_image, 'new_data' => $data['logo_image']->getClientOriginalName(), 'reason' => 'unable to push data to DB'];
                }
            }

            if (!empty($errors)) {
                // error
                return ['errors' => $errors, 'unchanged' => $unchanged, 'changed' => $changed, 'input_data' => $data];
            } 

            if (!empty($changed)) {
                //success
                $changed_fields = implode(', ', array_keys($changed));
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Updated fields: '.$changed_fields);
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made.');
            }

        }
    }

    public static function toggleFooter($request)
    {
        $results = [];

        if (isset($request['type'])) {
            $type_num = htmlspecialchars($request['type']);
            if ($type_num == 1) {
                $type = 'footer_enable';
            } elseif ($type_num == 2) {
                $type = 'footer_left_enable';
            } elseif ($type_num == 3) {
                $type = 'footer_right_enable';
            }

            if (isset($request['value'])) {
                $value = htmlspecialchars($request['value']);
                if ((int)$value == 0 || (int)$value == 1) {
                    
                    $current_data = DB::table('config')
                            ->select($type)
                            ->where('id', 1)
                            ->first();

                    if ($current_data) {
                        $previous_value = $current_data->$type;

                        $state = $value == 1 ? 'enabled' : 'disabled';

                        $update = DB::table('config')->where('id', 1)->update([$type => (int)$value, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => GeneralModel::getUser(),
                                'table' => 'config',
                                'record_id' => 1,
                                'action' => 'Update record',
                                'field' => $type,
                                'previous_value' => $previous_value,
                                'new_value' => (int)$value
                            ];

                            GeneralModel::updateChangelog($changelog_info);
                            $results[] = FunctionsModel::ajaxMsg("Footer $state! Please refresh.", 'success');
                        } else {
                            $results[] = FunctionsModel::ajaxMsg("Unable to get update config.", 'error');
                        }
                    } else {
                        $results[] = FunctionsModel::ajaxMsg("Unable to get current config.", 'error');
                    }
                } else {
                    $results[] = FunctionsModel::ajaxMsg('Invalid value specified.', 'error');
                }
            } else {
                $results[] = FunctionsModel::ajaxMsg('No value specified.', 'error');
            }

        } else {
            $results[] = FunctionsModel::ajaxMsg('No type specified.', 'error');
        }

        echo(json_encode($results));
    }

    static public function toggleAuth($request) 
    {
        $return = [];
        if (isset($request['id'])) {

            if (isset($request['value'])) {
                $value = htmlspecialchars($request['value']);

                if (($request['id'] == "2fa_enabled" || $request['id'] == "2fa_enforced" || $request['id'] == "signup_allowed") && ((int)$value == 1 || (int)$value == 0)) {
                    $field = $request['id'];
                
                    $current_data = DB::table('config')
                            ->select($field)
                            ->where('id', 1)
                            ->first();

                    if ($current_data) {
                        $previous_value = $current_data->$field;

                        $update = DB::table('config')->where('id', 1)->update([$field => (int)$value, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => GeneralModel::getUser(),
                                'table' => 'config',
                                'record_id' => 1,
                                'action' => 'Update record',
                                'field' => $field,
                                'previous_value' => $previous_value,
                                'new_value' => (int)$value
                            ];

                            GeneralModel::updateChangelog($changelog_info);
                            $return[0] = '<or class="green">Authentication updated.</or>';
                        } else {
                            $return[0] = '<or class="red">No changes made. Unable to update config.</or>';
                        }
                    } else {
                        $return[0] = '<or class="red">No changes made. Unable to get current config.</or>';
                    }
                } else {
                    $return[0] = '<or class="red">Incorrect field.</or>';
                }

            } else {
                $return[0] = '<or class="red">No Value Set</or>';
            }
        } else {
            $return[0] = '<or class="red">No ID set</or>';
        }
        $return['status'] = 'true';
        echo json_encode($return);
    }

    static public function userPermissionsChange($request)
    {
        $user_id = $request['user_id'];
        $anchor = 'users-settings';
        $errors = [];
        $unchanged = [];
        $changed = [];

        if ($user_id == 1) {
            return 'Error: Cannot update root user.';
        }

        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return 'Permission denied.';
        } 

        $permissions_fields = Schema::getColumnListing('users_permissions');

        // get current user
        $user_data = DB::table('users')
                            ->where('id', $user_id)
                            ->first();

        if ($user_data) {
            // user exists
            // get permissions 
            $current_permissions = DB::table('users_permissions')
                            ->where('id', $user_id)
                            ->first();

            if ($current_permissions) {
                // permissions exist
                $missing_keys = array_diff($permissions_fields, array_keys($request));

                foreach ($missing_keys as $key) {
                    $request[$key] = 0;
                }

                unset($request['id'], $request['user_id'], $request['_token'], $request['updated_at'], $request['created_at'], $request['user-permissions-submit'], $request['root']);
                // dd($request, $permissions_fields);
                foreach($request as $key => $value) {
                    if (!in_array($key, $permissions_fields)) {
                        // throw an error
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unknown key specified.');
                    }
                }

                foreach($request as $key => $value) {
                    $previous_value = $current_permissions->$key;
                    if ($value == 'on') {
                        $value = 1;
                    } elseif ($value == 'off') {
                        $value = 0;
                    }
                    if ((int)$value !== (int)$previous_value) {
                        if (in_array((int)$value, [1,0])) {
                            

                            $update = DB::table('users_permissions')->where('id', $user_id)->update([$key => (int)$value, 'updated_at' => now()]);

                            if ($update) {
                                // changelog
                                $changelog_info = [
                                    'user' => GeneralModel::getUser(),
                                    'table' => 'users_permissions',
                                    'record_id' => $user_id,
                                    'action' => 'Update record',
                                    'field' => $key,
                                    'previous_value' => $previous_value,
                                    'new_value' => (int)$value
                                ];

                                GeneralModel::updateChangelog($changelog_info);
                                // add to update array
                                $changed[$key] = ['current' => $current_permissions->$key, 'new_data' => $value, 'reason' => 'updated'];
                            } else {
                                $errors[$key] = 'failed to update';
                                $unchanged[$key] = ['current' => $current_permissions->$key, 'new_data' => $value, 'reason' => 'failed to update'];
                            }
                        } else {
                            // throw error - wrong integer value
                            $errors[$key] = 'incorrect value';
                            $unchanged[$key] = ['current' => $current_permissions->$key, 'new_data' => $value, 'reason' => 'wrong integer value'];
                        }
                    } else {
                        $unchanged[$key] = ['current' => $current_permissions->$key, 'new_data' => $value, 'reason' => 'no change'];
                    }
                }

            } else {
                // error - no permissions found
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'User\'s permissions not found in DB.');
            }
            

        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'User not found in DB.');
        }

        if (!empty($errors)) {
                // error
                return ['errors' => $errors, 'unchanged' => $unchanged, 'changed' => $changed, 'input_data' => $request];
            } 

            if (!empty($changed)) {
                //success
                $changed_fields = implode(', ', array_keys($changed));
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Updated fields: '.$changed_fields);
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made.');
            }
    }

    static public function userEnabled($request) 
    {
        $user_id = $request['user_id'];
        if ($user_id == 1) {
            return 'Error: Cannot update root user.';
        }

        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return 'Permission denied.';
        }

        // get current user
        $current_data = DB::table('users')
                            ->where('id', $user_id)
                            ->first();

        if ($current_data) {
            // user exists
            $previous_value = $current_data->enabled;

            if (in_array((int)$request['user_new_enabled'], [1,0])) {
                $update = DB::table('users')->where('id', $user_id)->update(['enabled' => (int)$request['user_new_enabled'], 'updated_at' => now()]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => GeneralModel::getUser(),
                        'table' => 'users',
                        'record_id' => $user_id,
                        'action' => 'Update record',
                        'field' => 'enabled',
                        'previous_value' => $previous_value,
                        'new_value' => (int)$request['user_new_enabled']
                    ];

                    if ((int)$request['user_new_enabled'] == 1) {
                        $state = 'enabled';
                    } else {
                        $state = 'disabled';
                    }

                    GeneralModel::updateChangelog($changelog_info);
                    return 'User: '.$current_data->username.' '.$state.'.';
                } else {
                    return 'No changes made. Unable to update state.';
                }
            } else {
                return 'Error: Non-boolean value present.';
            }

        } else {
            return 'Error: Unable to get current data';
        }
    }

    static public function forcePasswordReset($request)
    {

        $user_id = $request['user_id'];
        if ($user_id == 1) {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Cannot update root user.');
        }

        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Permission denied.');
        }

        $new_password = Hash::make($request['password']);

        // get current user
        $current_data = DB::table('users')
                            ->where('id', $user_id)
                            ->first();

        if ($current_data) {
            $update = DB::table('users')->where('id', $user_id)->update(['password' => $new_password, 'password_expired' => 1, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => GeneralModel::getUser(),
                    'table' => 'users',
                    'record_id' => $user_id,
                    'action' => 'Update record',
                    'field' => 'password',
                    'previous_value' => '********',
                    'new_value' => '********'
                ];

                GeneralModel::updateChangelog($changelog_info);
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('success', 'Password reset for user: '.$current_data->username);
            } else {
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'No changes made. Unable to update password');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Unable to confirm user.');
        }
    }

    static public function force2FAReset($request)
    {

        $user_id = $request['user_id'];
        if ($user_id == 1) {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Cannot update root user.');
        }

        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Permission denied.');
        }

        // get current user
        $current_data = DB::table('users')
                            ->where('id', $user_id)
                            ->first();

        if ($current_data) {
            $update = DB::table('users')->where('id', $user_id)->update(['2fa_secret' => NULL,'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => GeneralModel::getUser(),
                    'table' => 'users',
                    'record_id' => $user_id,
                    'action' => 'Update record',
                    'field' => '2fa_secret',
                    'previous_value' => '********',
                    'new_value' => ''
                ];
                
                GeneralModel::updateChangelog($changelog_info);
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('success', '2FA secret reset for user: '.$current_data->username);
            } else {
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'No changes made. Unable to reset 2FA secret');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Unable to confirm user.');
        }
    }

    static public function attributeDelete($request)
    {
        $attribute = $request['attribute-type'];
        $id = $request['id'];
        $allowed_types = ['tag', 'manufacturer'];
        $allowed_optic_types = ['optic_vendor', 'optic_type', 'optic_speed', 'optic_connector', 'optic_distance'];
        
        if (in_array($attribute, $allowed_types)) {
            $anchor = "attributemanagement-settings";
            $search_table = 'item';
        } elseif (in_array($attribute, $allowed_optic_types)) {
            $anchor = "opticattributemanagement-settings";
            $search_table = 'optic_item';
        } else {
            return redirect()->to(route('admin'))->with('error', 'Unknown attribute field');
        }

        if ($attribute == 'tag') {
            $search_table = 'stock_tag';
        }

        // check permissions
        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }

        // get current data
        $current_data = DB::table($attribute)
                            ->where('id', $id)
                            ->where('deleted', 0)
                            ->first();

        if ($current_data) {
            //remove the optic_ from the attribute
            if (str_contains($attribute, 'optic_')) {
                $clean_attribute = str_replace('optic_', '', $attribute);
            } else {
                $clean_attribute = $attribute;
            }

            // check for any links
            $links = DB::table($search_table)
                        ->where($clean_attribute.'_id', $id)
                        ->get()
                        ->toArray();

            if (!$links || empty($links)) {
                //update
                $update = DB::table($attribute)->where('id', $id)->update(['deleted' => 1, 'updated_at' => now()]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => GeneralModel::getUser(),
                        'table' => $attribute,
                        'record_id' => $id,
                        'action' => 'Delete record',
                        'field' => 'deleted',
                        'previous_value' => $current_data->deleted,
                        'new_value' => 1
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', ucwords($attribute).' attribute deleted: '.$current_data->name);
                } else {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made. Unable to delete attribute');
                }
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made. Links still present.');
            }
            
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to confirm attribute data.');
        }
    }

    static public function attributeRestore($request)
    {
        $attribute = $request['attribute-type'];
        $id = $request['id'];
        $allowed_types = ['tag', 'manufacturer'];
        $allowed_optic_types = ['optic_vendor', 'optic_type', 'optic_speed', 'optic_connector', 'optic_distance'];
        
        if (in_array($attribute, $allowed_types)) {
            $anchor = "attributemanagement-settings";
        } elseif (in_array($attribute, $allowed_optic_types)) {
            $anchor = "opticattributemanagement-settings";
        } else {
            return redirect()->to(route('admin'))->with('error', 'Unknown attribute field');
        }

        // check permissions
        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }

        // get current data
        $current_data = DB::table($attribute)
                            ->where('id', $id)
                            ->where('deleted', 1)
                            ->first();

        if ($current_data) {
            //update
            $update = DB::table($attribute)->where('id', $id)->update(['deleted' => 0, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => GeneralModel::getUser(),
                    'table' => $attribute,
                    'record_id' => $id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $current_data->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', ucwords($attribute).' attribute restored: '.$current_data->name);
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made. Unable restore attribute');
            }
            
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to confirm attribute data.');
        }
    }

    static public function toggleCost($request)
    {
        $results = [];

        if (isset($request['type'])) {
            $type_num = htmlspecialchars($request['type']);
            if ($type_num == 1) {
                $type = 'cost_enable_normal';
                $reply_text = 'Stock cost';
            } elseif ($type_num == 2) {
                $type = 'cost_enable_cable';
                $reply_text = 'Cable cost';
            } else {
                $results[] = FunctionsModel::ajaxMsg("Unknown type", 'error');
                exit();
            }
            if (isset($request['value'])) {
                $value = htmlspecialchars($request['value']);
                if ((int)$value == 0 || (int)$value == 1) {
                    
                    $current_data = DB::table('config')
                            ->select($type)
                            ->where('id', 1)
                            ->first();

                    if ($current_data) {
                        $previous_value = $current_data->$type;

                        $state = $value == 1 ? 'enabled' : 'disabled';

                        $update = DB::table('config')->where('id', 1)->update([$type => (int)$value, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => GeneralModel::getUser(),
                                'table' => 'config',
                                'record_id' => 1,
                                'action' => 'Update record',
                                'field' => $type,
                                'previous_value' => $previous_value,
                                'new_value' => (int)$value
                            ];

                            GeneralModel::updateChangelog($changelog_info);
                            $results[] = FunctionsModel::ajaxMsg("$reply_text $state!", 'success');
                        } else {
                            $results[] = FunctionsModel::ajaxMsg("Unable to get update config.", 'error');
                        }
                    } else {
                        $results[] = FunctionsModel::ajaxMsg("Unable to get current config.", 'error');
                    }
                } else {
                    $results[] = FunctionsModel::ajaxMsg('Invalid value specified.', 'error');
                }
            } else {
                $results[] = FunctionsModel::ajaxMsg('No value specified.', 'error');
            }

        } else {
            $results[] = FunctionsModel::ajaxMsg('No type specified.', 'error');
        }

        echo(json_encode($results));
    }

    public static function toggleNotification($request)
    {
        $results = [];

        if (isset($request['id']) && is_numeric($request['id'])) {

            if (isset($request['value'])) {
                $value = htmlspecialchars($request['value']);
                if ((int)$value == 0 || (int)$value == 1) {
                    
                    $current_data = DB::table('notifications')
                            ->select(['enabled', 'title'])
                            ->where('id', (int)$request['id'])
                            ->first();

                    if ($current_data) {
                        $previous_value = $current_data->enabled;

                        $state = $value == 1 ? 'enabled' : 'disabled';

                        $update = DB::table('notifications')->where('id', (int)$request['id'])->update(['enabled' => (int)$value, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => GeneralModel::getUser(),
                                'table' => 'notifications',
                                'record_id' => (int)$request['id'],
                                'action' => 'Update record',
                                'field' => 'enabled',
                                'previous_value' => $previous_value,
                                'new_value' => (int)$value
                            ];

                            GeneralModel::updateChangelog($changelog_info);
                            $results[] = FunctionsModel::ajaxMsg("Notification: '".$current_data->title."'   $state!", 'success');
                        } else {
                            $results[] = FunctionsModel::ajaxMsg("Unable to get update notifications.", 'error');
                        }
                    } else {
                        $results[] = FunctionsModel::ajaxMsg("Unable to get current notifications.", 'error');
                    }
                } else {
                    $results[] = FunctionsModel::ajaxMsg('Invalid value specified.', 'error');
                }
            } else {
                $results[] = FunctionsModel::ajaxMsg('No value specified.', 'error');
            }

        } else {
            $results[] = FunctionsModel::ajaxMsg('No type specified.', 'error');
        }

        echo(json_encode($results));
    }

    static public function stockLocationEdit($request)
    {
        $anchor = 'stocklocations-settings';
        $errors = [];
        $unchanged = [];
        $changed = [];

        $id = $request['id'];
        $type = $request['type'];
        
        // check permissions
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }        

        if (in_array($type, ['site','area','shelf'])) {        
            $table_fields = Schema::getColumnListing($type);
            $excluded_keys = ['id', '_token', 'location-edit-submit', 'type'];

            if ($type == 'shelf') {
                $excluded_keys[] = 'site_id';
                $excluded_keys[] = 'description';
            }

            $current_data = DB::table($type)
                    ->where('id', (int)$id)
                    ->first();
            
            foreach($excluded_keys as $key) {
                unset($request[$key]);
            }

            foreach($request as $field => $value) {
                if (!in_array($field, $excluded_keys)) { // to stop the _token and submit keys
                    if (!in_array($field, $table_fields)) {
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unknown table field: '.$field.'.');
                    }
                } else {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Excluded field: '.$field.'.');
                }
            }

            if ($current_data) {
                
                foreach($request as $field => $value) {
                    $previous_value = $current_data->$field;
                    
                    if ($value !== $previous_value) {
                        $update = DB::table($type)->where('id', (int)$id)->update([$field => $value, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => GeneralModel::getUser(),
                                'table' => $type,
                                'record_id' => (int)$id,
                                'action' => 'Update record',
                                'field' => $field,
                                'previous_value' => $previous_value,
                                'new_value' => $value
                            ];

                            $changed[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'updated'];

                            GeneralModel::updateChangelog($changelog_info);
                        } else {
                            $errors[$field] = 'failed to update';
                            $unchanged[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'unable to push data to DB'];
                        }
                    } else {
                        $unchanged[$field] = ['current' => $current_data->$field, 'new_data' => $value, 'reason' => 'no changes to be made'];
                    }
                }
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to get current data.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid type.');
        }

        if (!empty($errors)) {
            // error
            return ['errors' => $errors, 'unchanged' => $unchanged, 'changed' => $changed, 'input_data' => $request];
        } 

        if (!empty($changed)) {
            //success
            $changed_fields = implode(', ', array_keys($changed));
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Updated fields: '.$changed_fields);
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made.');
        }
    }

    static public function stockLocationDelete($request)
    {
        $anchor = 'stocklocations-settings';

        $id = $request['id'];
        $type = $request['type'];

        // check permissions
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }     

        if (in_array($type, ['site', 'area', 'shelf'])) {
            $current_data = DB::table($type)
                    ->where('id', (int)$id)
                    ->first();

            if ($current_data) {
                // check if shelf is already deleted
                if ((int)$current_data->deleted == 1) {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Delete failed: '.ucwords($type).' already deleted.');
                }

                // check for any existing links
                switch ($type) {
                    case 'site': 
                        $search_tables = ['area', 'optic_item'];
                        break;
                    case 'area':
                        $search_tables = ['shelf'];
                        break;
                    case 'shelf':
                        $search_tables = ['item', 'container'];
                        break;
                    default:
                        $search_tables = [];
                }

                foreach($search_tables as $table) {
                    $links = AdminModel::attributeLinks($table, $type.'_id', null, 1, [$type.'_id' => $id]);
                    if (array_key_exists($id, $links) && $links[$id]['count'] > 0) {
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to delete: Links still present in '.$table.' table.');
                    }
                }

                $previous_value = $current_data->deleted;

                if ($previous_value !== 1) {
                    $update = DB::table($type)->where('id', (int)$id)->update(['deleted' => 1, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => GeneralModel::getUser(),
                            'table' => $type,
                            'record_id' => (int)$id,
                            'action' => 'Delete record',
                            'field' => 'deleted',
                            'previous_value' => 0,
                            'new_value' => 1
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', ucwords($type).': '.$current_data->name.' deleted.');
                    } else {
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to update database entry.');
                    }
                } else {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to delete: '.ucwords($type).' already deleted.');
                }
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to get current data.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid type.');
        }
    }

    static public function stockLocationRestore($request)
    {
        $anchor = 'stocklocations-settings';

        $id = $request['id'];
        $type = $request['type'];

        // check permissions
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }     

        if (in_array($type, ['site', 'area', 'shelf'])) {
            $current_data = DB::table($type)
                    ->where('id', (int)$id)
                    ->first();

            if ($current_data) {
                // check if shelf is already active
                if ((int)$current_data->deleted == 0) {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Restore failed: '.ucwords($type).' already ative.');
                }

                // check for any existing links
                switch ($type) {
                    case 'site': 
                        $search_tables = [];
                        $q_field = '';
                        break;
                    case 'area':
                        $search_tables = ['site'];
                        $q_field = 'site_id';
                        break;
                    case 'shelf':
                        $search_tables = ['area'];
                        $q_field = 'area_id';
                        break;
                    default:
                        $search_tables = [];
                }

                if (!empty($search_tables)) {
                    foreach($search_tables as $table) {
                        $data = DB::table($table)
                                ->where('id', (int)$current_data->$q_field)
                                ->where('deleted', 0)
                                ->first();
                        if (!$data) {
                            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Restore failed: Parent is deleted.');
                        }
                    }
                }

                $previous_value = $current_data->deleted;

                if ($previous_value !== 0) {
                    $update = DB::table($type)->where('id', (int)$id)->update(['deleted' => 0, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => $user,
                            'table' => $type,
                            'record_id' => (int)$id,
                            'action' => 'Restore record',
                            'field' => 'deleted',
                            'previous_value' => 1,
                            'new_value' => 0
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', ucwords($type).': '.$current_data->name.' restored.');
                    } else {
                        return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to update database entry.');
                    }
                } else {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to delete: '.ucwords($type).' already active.');
                }
            } else {
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to get current data.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid type.');
        }
    }

    static public function stockLocationAdd($request)
    {
        $authorized = 0;
        $anchor = 'stocklocations-settings';
        $type = $request['type'];
        $parent_id = $request['parent'] ?? '';

        // check permissions
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            $authorized = 1;
        }  

        if (in_array($type, ['site', 'area', 'shelf'])) {
            // correct type
            if ($type == 'site' && $authorized == 0) {
                // unauthorized.
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid permissions.');
            }

            $values = ['name' => $request['name'], 'deleted' => 0, 'created_at' => now(), 'updated_at' => now()];

            switch ($type) {
                case 'site': 
                    $parent_table = '';
                    $values['description'] = $request['description'];
                    break;
                case 'area':
                    $parent_table = 'site';
                    $values['description'] = $request['description'];
                    $values[$parent_table.'_id'] = $parent_id;
                    break;
                case 'shelf':
                    $parent_table = 'area';
                    $values[$parent_table.'_id'] = $parent_id;
                    break;
                default:
                    $parent_table = '';
            }

            if (filled($parent_table)) {
                // check if the parent exists and isnt deleted.
                $parent = DB::table($parent_table)
                                ->where('id', $parent_id)
                                ->where('deleted', 0)
                                ->first();
                if (!$parent) {
                    // parent doesnt exist or is deleted.
                    return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid parent.');
                }
            }

            // ADD
            $insert = DB::table($type)->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => $type,
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'name',
                    'previous_value' => '',
                    'new_value' => $request['name']
                ];

                GeneralModel::updateChangelog($changelog_info);
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', ucwords($type).' added: '.$request['name'].' with id: '.$insert.'.');
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to insert database entry.');
            }
        
        } else {
            // incorrect type
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Invalid type.');
        }
    }

    static public function imageManagementLoad($request)
    {
        $path = 'img/stock';
        $limit = 20; // how many to load each refresh
        $page = $request['page'];
        $current_page = $request['current_page'];

        $files = GeneralModel::getFilesInDirectory($path);

        $table_rows = []; 

        if ($page == -1) {
            // load ALL
            $start = 0;
            $end = count($files);
        } else {
            // load the $limit
            if ($page == 0) {
                $page = 1;
            }
            $start = ($page*$limit)-$limit;
            $end = $start+$limit;
            if ($end > count($files)) {
                $end = count($files);
            }
        }

        if (count($files) > $end) {
            $nextPage = $page+1;
        } else {
            $nextPage = -1;
        }

        for ($f=$start; $f<$end; $f++) {
            $filename = $files[$f];

            // get attribute links
            $links = AdminModel::attributeLinks('stock_img', 'image', 'id, stock_id, image', 0, ['image' => $filename]);

            if ($links && !empty($links[$filename]['rows'])) {
                $link_count = $links[$filename]['count'];
                $disabled = 'disabled title="Image still linked to stock. Remove these links before deleting."';
                $links_button = '<button class="btn btn-warning" id="image-'.$f.'-links" type="button" onclick="showLinks(\'image\', \''.$f.'\')">Show Links</button>';
            } else {
                $link_count = 0;
                $disabled = $links_button = '';
            }
        

            $table_rows[] = '<tr id="image-row-'.$f.'" class="align-middle">
                                <form enctype="multipart/form-data" id="image-row-'.$f.'-form" action="'.route('admin.imageManagementSettings').'" method="POST">
                                    <input type="hidden" name="_token" form="image-row-'.$f.'-form" value="'.csrf_token().'" />
                                    <input type="hidden" name="file-name" form="image-row-'.$f.'-form" value="'.$filename.'" />
                                    <input type="hidden" name="file-links" form="image-row-'.$f.'-form" value="'.$link_count.'" />
                                    <td id="image-'.$f.'-thumb" class="text-center align-middle" style="width:130px"><img id="image-'.$f.'-img" class="inv-img-main thumb" alt="'.$filename.'" src="'.$path.'/'.$filename.'" onclick="modalLoad(this)"></td>
                                    <td id="image-'.$f.'-name" class="text-center align-middle">'.$path.'/'.$filename.'</td>
                                    <td class="text-center align-middle">'.$link_count.'</td>
                                    <td class="text-center align-middle"><button class="btn btn-danger" type="submit" form="image-row-'.$f.'-form" name="imagemanagement-delete-submit" '.$disabled.'><i class="fa fa-trash"></i></button></td>
                                    <td class="text-center align-middle">'.$links_button.'</td>
                                </form>
                            </tr>
                        ';
        
            if ($links && !empty($links[$filename]['rows'])) {
                $sub_rows = '';

                foreach($links[$filename]['rows'] as $row) {
                    $stock_data = DB::table('stock')
                            ->where('id', $row['stock_id'])
                            ->first();
                    if ($stock_data) {
                        $sub_rows .= '<tr class="clickable" onclick=navPage("'.route('stock', ['stock_id' => $row['stock_id']]).'")>
                                        <td>'.$row['id'].'</td>
                                        <td><a href="'.route('stock', ['stock_id' => $row['stock_id']]).'">'.$row['stock_id'].'</a></td>
                                        <td><a href="'.route('stock', ['stock_id' => $row['stock_id']]).'">'.$stock_data->name.'</a></td>
                                        <td>'.$row['image'].'</td>
                                    </tr>';
                    } else {
                        $sub_rows .= '<tr class="clickable" onclick=navPage("stock.php?stock_id='.$row['stock_id'].'")>
                                        <td colspan=100%>Unable to get stock data for id: '.$row['stock_id'].'</td>
                                    </tr>';
                    }
                    
                }
                $table_rows[] = '<tr id="image-row-'.$f.'-links" class="align-middle" hidden>
                                    <td colspan=100%>
                                        <div>
                                            <table class="table table-dark theme-table">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th>ID</th>
                                                        <th>Stock ID</th>
                                                        <th>Stock Name</th>
                                                        <th>Image</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                '.$sub_rows.'
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>';
            }
        }

        if (empty($table_rows)) {
            $table_rows[0] = "ERROR";
        } else {
            if ($nextPage !== -1) {
                $table_rows[] = '<tr id="loader-tr">
                                    <td id="loader-td" colspan=100% class="algin-middle text-center">
                                        <div id="loader-outerdiv">
                                            <button class="btn btn-info" id="show-images" onclick="loadAdminImages('.$current_page.', '.$nextPage.')">Load More Images</button>
                                            <div class="loader" id="loaderDiv" style="margin-top:10px;width:130px;display:none">
                                                <div class="loaderBar"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>';
            }
        }
        print_r(json_encode($table_rows));
    }

    static public function imageManagementDelete($request)
    {
        $filename = $request['file-name'];
        $file_links = $request['file-links'];
        $path_name = public_path('img/stock').'/'.$filename;

        // get the stock_img entry
        $db_entries = DB::table('stock_img')
                            ->where('image', $filename)
                            ->first();

        if ((!$db_entries || empty($db_entries)) && $file_links == 0) {
            // check if the image exists in the folder
            if (file_exists($path_name)) {
                // file exists
                if (File::delete($path_name)) {
                    return redirect()->to(route('admin', ['section' => 'imagemanagement-settings']) . '#imagemanagement-settings')->with('success', 'File deleted: '.$filename.'.');
                } else {
                    return redirect()->to(route('admin', ['section' => 'imagemanagement-settings']) . '#imagemanagement-settings')->with('error', 'Failed to delete file: '.$path_name.'.');
                }
            } else {
                // file doesnt exists
                return redirect()->to(route('admin', ['section' => 'imagemanagement-settings']) . '#imagemanagement-settings')->with('error', 'File doesnt exist.');
            }
        } else {
             return redirect()->to(route('admin', ['section' => 'imagemanagement-settings']) . '#imagemanagement-settings')->with('error', 'Image not delete. Links exist.');
        }
    }

    static public function getPermissionPreset($id)
    {
        $preset = DB::table('users_permissions_roles')->where('id', $id)->first();
        $user = Generalmodel::getUser();

        if ($preset) {
            $array = (array)$preset;

            // Remove unwanted keys
            unset($array['id'], $array['name'], $array['created_at'], $array['updated_at']);

            // Convert to an array of key/value pairs
            $result = [];
            foreach ($array as $key => $value) {
                $result[] = ['key' => $key, 'value' => $value];
            }

            return $result;
            
        }

        return [];
    }

    static public function addPermissionPreset($request)
    {
       
        $anchor = 'userspermissionspresets-settings';

        $user = GeneralModel::getUser();

        $permissions_fields = (array)Schema::getColumnListing('users_permissions_roles');

        unset($request['user_permissions_preset_add'], $request['_token']);
        unset($permissions_fields['updated_at'], $permissions_fields['created_at'], $permissions_fields['id'], $permissions_fields['name']);

        $values = ['created_at' => now(), 'updated_at' => now()];

        foreach($request as $key => $value) {
            if (!in_array($key, $permissions_fields)) {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unknown key specified.');
            }

            if ($value == 'on') {
                $values[$key] = 1;
            } elseif ($value == 'off') {
                $values[$key] = 0;
            } else {
                $values[$key] = $value;
            }
        }

        foreach ($permissions_fields as $field) {
            if (!array_key_exists($field, $values)) {
                $values[$field] = 0;
            }
        }


        $find = DB::table('users_permissions_roles')->where('name', $request['name'])->first();

        if (!$find) {
            // ADD
            $insert = DB::table('users_permissions_roles')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'users_permissions_roles',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'name',
                    'previous_value' => '',
                    'new_value' => $values['name']
                ];

                GeneralModel::updateChangelog($changelog_info);
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Preset added: '.$request['name'].' with id: '.$insert.'.');
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to insert database entry.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Name in use.');
        }
    }

    public static function updateEmailTemplate($request)
    {
        $user = GeneralModel::getUser();
        $template_data = GeneralModel::getFirstWhere('email_templates', ['id' => $request['template_id'], 'slug' => $request['slug']]);

        if ($template_data){
            // template exists
            // update the content
            $update_count = 0;
            if ($template_data['subject'] !== $request['subject']) {
                // update
                $update = DB::table('email_templates')->where('id', $request['template_id'])->update(['subject' => $request['subject'], 'updated_at' => now()]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'email_templates',
                        'record_id' => $request['template_id'],
                        'action' => 'Modify record',
                        'field' => 'subject',
                        'previous_value' => $template_data['subject'],
                        'new_value' => $request['subject']
                    ];
                    GeneralModel::updateChangelog($changelog_info);
                    $update_count++;
                }
            }

            if ($template_data['body'] !== $request['body']) {
                // update
                $update = DB::table('email_templates')->where('id', $request['template_id'])->update(['body' => $request['body'], 'updated_at' => now()]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'email_templates',
                        'record_id' => $request['template_id'],
                        'action' => 'Modify record',
                        'field' => 'body',
                        'previous_value' => $template_data['body'],
                        'new_value' => $request['body']
                    ];
                    GeneralModel::updateChangelog($changelog_info);
                    $update_count++;
                }
            }

            if ($update_count > 0) {
                return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('success', 'Template updated.');
            } else {
                return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('error', 'Nothing to update.');
            }

        } else {
            return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('error', 'Unable to find current template data.');
        }
    }

    public static function restoreEmailTemplate($request)
    {
        $user = GeneralModel::getUser();
        $template_data = GeneralModel::getFirstWhere('email_templates', ['id' => $request['template_id'], 'slug' => $request['slug']]);
        $default_template_data = GeneralModel::getFirstWhere('email_templates_default', ['id' => $request['template_id'], 'slug' => $request['slug']]);

        if ($template_data){
            // template exists
            if ($default_template_data) {
                // update the content
                $update_count = 0;
                if ($template_data['subject'] !== $default_template_data['subject']) {
                    // update
                    $update = DB::table('email_templates')->where('id', $request['template_id'])->update(['subject' => $default_template_data['subject'], 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => $user,
                            'table' => 'email_templates',
                            'record_id' => $request['template_id'],
                            'action' => 'Restore record',
                            'field' => 'subject',
                            'previous_value' => $template_data['subject'],
                            'new_value' => $default_template_data['subject']
                        ];
                        GeneralModel::updateChangelog($changelog_info);
                        $update_count++;
                    }
                }

                if ($template_data['body'] !== $default_template_data['body']) {
                    // update
                    $update = DB::table('email_templates')->where('id', $request['template_id'])->update(['body' => $default_template_data['body'], 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => $user,
                            'table' => 'email_templates',
                            'record_id' => $request['template_id'],
                            'action' => 'Restore record',
                            'field' => 'body',
                            'previous_value' => $template_data['body'],
                            'new_value' => $default_template_data['body']
                        ];
                        GeneralModel::updateChangelog($changelog_info);
                        $update_count++;
                    }
                }

                if ($update_count > 0) {
                    return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('success', 'Template restored to default.');
                } else {
                    return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('error', 'Template is already the default.');
                }

            } else {
                return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('error', 'Unable to find default template data.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'emailtemplates-settings']) . '#emailtemplates-settings')->with('error', 'Unable to find current template data.');
        }
    }

    static public function addLocalUser($user_data=[], $permissions_data=[])
    {
        $user_data['email_verified_at'] = now();
        $user_data['password_expired'] = 1;

        /** @var User $new_user */
        $new_user = User::create($user_data);

        if ($new_user) {
            // send welcome email:
            SmtpModel::notificationEmail(1, 1, []);

            $user_id = $new_user->id; 

            // add a changelog for the user being added
            $user = GeneralModel::getUser();
            $info = [
                'user' => $user,
                'table' => 'users',
                'record_id' => $user_id,
                'field' => 'username',
                'new_value' => $new_user->username,
                'action' => 'Add user',
                'previous_value' => '',
            ];
            GeneralModel::updateChangelog($info);
            
            // add permissions to table

            //convert to boolean
            $permissions_data_bool = ['id' => $user_id];

            foreach($permissions_data as $key => $value) {
                if ($value == 'on') {
                    $permissions_data_bool[$key] = 1;
                } elseif ($value == 'off') {
                    $permissions_data_bool[$key] = 0;
                }
            }

            $insert = DB::table('users_permissions')->insertGetId($permissions_data_bool);

            if ($insert) {
                // inserted ok

                if ($insert == $user_id) {
                    // correct id

                    // changelog
                    unset($permissions_data_bool['id']);
                    foreach($permissions_data_bool as $key => $value) {
                        $changelog_info = [
                            'user' => $user,
                            'table' => 'users_permissions',
                            'record_id' => $user_id,
                            'action' => 'New record',
                            'field' => $key,
                            'previous_value' => 'off',
                            'new_value' => (int)$value
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                    }

                    // redirect
                    return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('success', 'User added.');
                } else {
                    // incorrect id
                    return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'User id ('.$user_id.') and permissions id ('.$insert.') do not match.');

                }

            } else {
                // didnt insert
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Permissions failed to add for user.');
            }
        } else {
            // user wasnt added. error return
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Unable to add user.');
        }
    }

}