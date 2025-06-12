<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as HttpRequest;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\TransactionModel;

/**
 * 
 *
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
                        
        return $instance->selectRaw('sl.id AS id, sl.user_id AS sl_user_id, 
                                    FROM_UNIXTIME(sl.login_time) AS sl_login_time, IFNULL(FROM_UNIXTIME(sl.logout_time), NULL) AS sl_logout_time, 
                                    COALESCE(INET_NTOA(sl.ipv4), INET6_NTOA(sl.ipv6)) AS sl_ip,
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

    static public function attributeLinks($search_table, $asset_field, $select=null, $deleted_count=null)
    {
        $return = [];

        $instance = new self();
        $instance->setTable($search_table);

        $results = $instance->when($select !== null, function ($query) use ($select) {
                                $query->selectRaw($select);
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
                        $value = Hash::make($value);
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
                $changed_fields = implode(',', array_keys($changed));
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

    static public function userRoleChange($request)
    {
        $user_id = $request['user_id'];
        if ($user_id == 1) {
            return 'Error: Cannot update root user.';
        }

        $user = GeneralModel::getUser();

        if (!in_array($user['role_id'], [1,3])) {
            return 'Permission denied.';
        }

        // get current user
        $current_data = DB::table('users')
                            ->where('id', $user_id)
                            ->first();

        if ($current_data) {
            // user exists
            $previous_value = $current_data->role_id;

            if (in_array((int)$request['user_new_role'], [1,0])) {
                $update = DB::table('users')->where('id', $user_id)->update(['role_id' => (int)$request['user_new_role'], 'updated_at' => now()]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => GeneralModel::getUser(),
                        'table' => 'users',
                        'record_id' => $user_id,
                        'action' => 'Update record',
                        'field' => 'role_id',
                        'previous_value' => $previous_value,
                        'new_value' => (int)$request['user_new_role']
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                    return 'Role updated for user: '.$current_data->username.'.';
                } else {
                    return 'Error: No changes made. Unable to update role.';
                }
            } else {
                return 'Error: Non-boolean value present.';
            }

        } else {
            return 'Error: Unable to get current data';
        }
    }

    static public function userEnabled($request) 
    {
        $user_id = $request['user_id'];
        if ($user_id == 1) {
            return 'Error: Cannot update root user.';
        }

        $user = GeneralModel::getUser();

        if (!in_array($user['role_id'], [1,3])) {
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

        if (!in_array($user['role_id'], [1,3])) {
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

        if (!in_array($user['role_id'], [1,3])) {
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

        if (!in_array($user['role_id'], [1,3])) {
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

        if (!in_array($user['role_id'], [1,3])) {
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

    static public function stockLocationSettings($request)
    {
        $anchor = 'stocklocations-settings';
        $errors = [];
        $unchanged = [];
        $changed = [];

        $id = $request['id'];
        $type = $request['type'];
        
        // check permissions
        $user = GeneralModel::getUser();
        if (!in_array($user['role_id'], [1,3])) {
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
            $changed_fields = implode(',', array_keys($changed));
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Updated fields: '.$changed_fields);
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made.');
        }
    }


}