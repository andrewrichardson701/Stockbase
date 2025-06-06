<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Models\GeneralModel;

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

    static public function updateGlobalSettings($data)
    {
        $errors = [];
        $unchanged = [];
        $changed = [];

        $user = GeneralModel::getUser();
        $config_fields = Schema::getColumnListing('config');
        $excluded_keys = ['_token', 'global-submit'];

        $current_data = DB::table('config')
                            ->where('id', 1)
                            ->first();

        if (isset($data['global-restore-defaults'])) {
            $reset_array = ['system_name', 'banner_color', 'logo_image', 'favicon_image', 
                            'currency', 'sku_prefix', 'base_url', 'default_theme_id'];
            $reset = AdminModel::resetConfig($reset_array);

            if ($reset == 1) {
                return redirect()->to(route('admin', ['section' => 'global-settings']) . '#global-settings')->with('success', 'Config Reset.');
            } else {
                return redirect()->to(route('admin', ['section' => 'global-settings']) . '#global-settings')->with('error', 'Reset failed.');
            }
        } 

        $changelog_info = [
                'user' => GeneralModel::getUser(),
                'table' => 'config',
                'record_id' => 1,
                'action' => 'Update record',
            ];

        unset($data['_token'], $data['global-submit']); // remove these to stop them being queried

        foreach($data as $field => $value) {
            if (!in_array($field, $excluded_keys)) { // to stop the _token and submit keys
                if (!in_array($field, $config_fields)) {
                    return redirect(GeneralModel::previousURL())->with('error', 'Unknown table field: '.$field.'.');
                }
            }
        }
        
        foreach($data as $field => $value) {
            if (!in_array($field, ['favicon_image', 'logo_image'])) {
                // not an image 
                if (ctype_digit($value)) { // checks for numbers and NOT decimals
                    $value = (int)$value;
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
                return redirect()->to(route('admin', ['section' => 'global-settings']) . '#global-settings')->with('success', 'Updated fields: '.$changed_fields);
            } else {
                return redirect()->to(route('admin', ['section' => 'global-settings']) . '#global-settings')->with('error', 'No changes made.');
            }

        }
    }

    public static function toggleFooter($request)
    {
        $results = [];

        function msg($text, $type) {
            if ($type == 'error') {
                $class="red";
            } else {
                $class="green";
            }
            $head = '<or class="'.$class.'">';
            $foot = '</or>';

            return $head.$text.$foot;
        }

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
                            $results[] = msg("Footer $state! Please refresh.", 'success');
                        } else {
                            $results[] = msg("Unable to get update config.", 'error');
                        }
                    } else {
                        $results[] = msg("Unable to get current config.", 'error');
                    }
                } else {
                    $results[] = msg('Invalid value specified.', 'error');
                }
            } else {
                $results[] = msg('No value specified.', 'error');
            }

        } else {
            $results[] = msg('No notification type specified.', 'error');
        }

        echo(json_encode($results));
    }

}