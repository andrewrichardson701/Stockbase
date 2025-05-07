<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel query()
 * @mixin \Eloquent
 */
class GeneralModel extends Model
{
    //
    static public function config() {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable('config');

        return $instance->where('id', '=', 1)
                        ->get()
                        ->toarray()[0];
    }

    static public function configDefault() { 
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable('config_default');

        return $instance->where('id', '=', 1)
                        ->get()
                        ->toarray()[0];
    }

    static public function allDistinct($table, $deleted=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable($table);

        return $instance->distinct()
                        ->when($deleted !== null, function ($query) use ($deleted) {
                            $query->where('deleted', '=', $deleted);
                        })
                        ->get()
                        ->toarray();
    }

    static public function allDistinctField($field, $table, $deleted=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable($table);

        return $instance->distinct()->select($field)
                        ->when($deleted !== null, function ($query) use ($deleted) {
                            $query->where('deleted', '=', $deleted);
                        })
                        ->get()
                        ->toarray();
    }

    static public function allDistinctAreas($site, $deleted=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable('area');

        return $instance->distinct()
                        ->where(function($query) use ($deleted) {
                            if ($deleted !== null) {
                                $query->where('deleted', '=', $deleted);
                            } else {
                                $query->whereRaw('`deleted` = `deleted`');
                            }
                        })
                        ->where(function($query) use ($site) {
                            if ($site !== 0 && $site !== '0') {
                                $query->where('area.site_id', '=', $site);
                            } else {
                                $query->whereRaw('1 = 1');
                            }
                        })
                        ->get()
                        ->toarray();
    }

    static public function getAllWhere($table, $params, $orderby = null)
    {
        $instance = new self();
        $instance->setTable($table);

        $query = $instance->newQuery(); // ✅ Start a new query builder

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query->where($key, $value); // ✅ Correctly applying `where()`
            }
        }

        return $query->orderBy($orderby ?? 'id') // ✅ Correctly chaining `orderBy()`
                    ->get()
                    ->toArray();
    }

    static public function getAllWhereNotIn($table, $params, $orderby=null)
    {
        $instance = new self();
        $instance->setTable($table);

        $query = $instance->newQuery(); // ✅ Start a new query builder

        if (!empty($params)) {
            foreach (array_keys($params) as $key) {
                $query->whereNotIn($key, $params[$key]);
            } 
        }

        return $query->orderBy($orderby ?? 'id') // Default to 'id' if $orderby is null
                    ->get()
                    ->toArray();
    }

    static public function allDistinctShelves($area, $deleted=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable('shelf');

        return $instance->distinct()
                        ->where(function($query) use ($deleted) {
                            if ($deleted !== null) {
                                $query->where('deleted', '=', $deleted);
                            } else {
                                $query->whereRaw('`deleted` = `deleted`');
                            }
                        })
                        ->where(function($query) use ($area) {
                            if ($area !== 0 && $area !== '0') {
                                $query->where('shelf.area_id', '=', $area);
                            } else {
                                $query->whereRaw('1 = 1');
                            }
                        })
                        ->get()
                        ->toarray();
    }

    static public function allDistinctShelvesByManufacturer($manufacturer, $stock_id, $deleted=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable('shelf');

        return $instance->select(['shelf.id as id', 
                                'shelf.name as shelf_name', 
                                'shelf.area_id as area_id', 
                                'shelf.deleted as shelf_deleted',
                                'area.name as area_name',
                                'site.name as site_name',
                                DB::raw('concat(site.name, ", ", area.name, ", ", shelf.name) as location')])
                        ->distinct()
                        ->join('item', 'item.shelf_id', 'shelf.id')
                        ->join('area', 'shelf.area_id', 'area.id')
                        ->join('site', 'area.site_id', 'site.id')
                        ->where(function($query) use ($deleted) {
                            if ($deleted !== null) {
                                $query->where('shelf.deleted', '=', $deleted);
                            } else {
                                $query->whereRaw('`deleted` = `deleted`');
                            }
                        })
                        ->where(function($query) use ($manufacturer) {
                            if ($manufacturer !== 0 && $manufacturer !== '0') {
                                $query->where('item.manufacturer_id', '=', $manufacturer);
                            } else {
                                $query->whereRaw('1 = 1');
                            }
                        })
                        ->where('item.deleted', '=', 0)
                        ->where('item.stock_id', '=', $stock_id)
                        ->get()
                        ->toArray();
    }

    static public function formatArrayOnId($array) 
    {
        $formatted = [];
        $formatted['rows'] = [];
        $i = 0;
        foreach ($array as $entry) { // format the data
            if(isset($entry['id'])){
                $formatted[$entry['id']] = $entry;
            } else {
                $formatted[$i] = $entry;
            }
            $i++;
        }
        return $formatted;
    }

    static public function formatArrayOnIdAndCount($array) 
    {
        $formatted = [];
        $formatted['rows'] = [];
        
        $count = count($array) ?? 0;
        $formatted['count'] = $count;

        $i = 0;

        foreach ($array as $entry) { // format the data
            if (isset($entry['deleted'])) {
                if (!isset($formatted['deleted_count'])) {
                    $formatted['deleted_count'] = 0;
                }
                if ($entry['deleted'] == 1) {
                    $formatted['deleted_count']++;
                }
            }
            if (isset($entry['id'])) {
                $formatted['rows'][$entry['id']] = $entry;
            } else {
                $formatted['rows'][$i] = $entry;
            }
            $i++;
        }
        
        return $formatted;
    }

    static public function versionNumber()
    {
        return "1.3.0L";
    }

    static public function getThemeFileName($theme_id)
    {
        
        $instance = new self();
        $instance->setTable('theme');

        return $instance->where('id', '=', $theme_id)
                        ->get()
                        ->toarray()[0];
    }

    public static function previousURL()
    {
        $previousUrl = url()->previous();

        // Fallback to root ("/") if the previous URL is empty or null
        if (empty($previousUrl)) {
            return '/';
        }

        $parsedUrl = parse_url($previousUrl);

        // Reconstruct the URL without the domain
        $combinedUrl = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        if (isset($parsedUrl['query'])) {
            $combinedUrl .= '?' . $parsedUrl['query'];
        }

        // Return the reconstructed URL
        return $combinedUrl;
    }

    static public function redirectURL($url, $params)
    {
        if (!empty($params)) {
            foreach (array_keys($params) as $key) {
                $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';

                // Append the parameters using http_build_query
                $url .= $separator . http_build_query($params);
            }
        }
        return $url;
    }

    static public function headData() 
    {
        
        $head_data = [];
        $head_data['default_config'] = GeneralModel::configDefault();
        $head_data['config'] = GeneralModel::config();
        $head_data['config_compare'] = GeneralModel::configCompare();
        $head_data['version_number'] = GeneralModel::versionNumber();
        $head_data['default_theme'] = isset($head_data['config']['default_theme_id']) ? GeneralModel::getThemeFileName($head_data['config']['default_theme_id']) : GeneralModel::getThemeFileName($head_data['default_config']['default_theme_id']);
        $head_data['requested_info'] = GeneralModel::requestedInfo();

        $head_data['previous_url'] = GeneralModel::previousURL();

        $head_data['update_data'] = GeneralModel::updateChecker($head_data['version_number']);

        // $head_data['active_user'] = GeneralModel::temp_activeUserData();
        
        $head_data['user'] = GeneralModel::getUser();

        $head_data['extras'] = [];
        $head_data['extras']['nav_secondary_color'] = FunctionsModel::adjustBrightness($head_data['config_compare']['banner_color'], -0.2);
        $head_data['extras']['banner_text_color'] = FunctionsModel::getWorB($head_data['config_compare']['banner_color']);
        $head_data['extras']['fav_btn_hover_bg'] = FunctionsModel::adjustBrightness($head_data['config_compare']['banner_color'], -0.1);
        $head_data['extras']['fav_btn_hover_text'] = FunctionsModel::getWorB($head_data['extras']['fav_btn_hover_bg']);
        $head_data['extras']['invert_banner_color'] = FunctionsModel::getComplement($head_data['config_compare']['banner_color']);
        $head_data['extras']['invert_banner_text_color'] = FunctionsModel::getWorB($head_data['extras']['invert_banner_color']);
        $head_data['extras']['default_banner_text_color'] = FunctionsModel::getWorB($head_data['default_config']['banner_color']);
        
        return $head_data;
    }

    static public function configCompare()
    {
        $config = GeneralModel::config();
        $config_default = GeneralModel::configDefault();
        $return_array = [];

        $keys = array_keys($config_default);

        $nullable_fields = array('smtp_password', 'smtp_host', 'smtp_username',
                                'ldap_host', 'ldap_host_secondary', 'ldap_port', 'ldap_basedn', 'ldap_usergroup', 'ldap_userfilter', 'ldap_password', 'ldap_domain');

        for ($k = 0; $k < count($keys); $k++) {
            $default = $config_default[$keys[$k]];
            $current = $config[$keys[$k]];
                
            if (!in_array($keys[$k], $nullable_fields)) {
                if ($current === null || $current === '') {
                    $current = $default;
                }
            }
            
            $return_array[$keys[$k]] = $current;
        }

        return $return_array;
    }

    static public function requestedInfo() 
    {
        $requestedUrl = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? (isset(explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1]) ? explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1] : '') : '';
        $requestedHttp = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ''; // IP of host server
        $requestedPort = isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : '';
        $requestedHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : '';
        $requestedServer = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : '';
        $remoteIP = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')));
        if (str_contains($remoteIP, ',')) {
            $remoteIP = strtok($remoteIP, ',');
        }

        $requestedUri = isset($_SERVER['HTTP_X_REQUEST_URI']) ? $_SERVER['HTTP_X_REQUEST_URI'] : '';

        $serverendhttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
            $queryString = $_SERVER['QUERY_STRING'];
            $queryStringUrl = '?'.$queryString;
        } else {
            $queryString = '';
            $queryStringUrl = '';
        }

        $fullRequestedURL = $requestedHttp.'://'.$requestedUrl.$requestedUri.$queryStringUrl;
        $platform = $_SERVER["HTTP_USER_AGENT"];

        $return = [
            'requested_url' => $requestedUrl,
            'requested_http' => $requestedHttp,
            'requested_port' => $requestedPort,
            'requested_host' => $requestedHost,
            'requested_server' => $requestedServer,
            'remote_ip' => $remoteIP,
            'requested_uri' => $requestedUri,
            'serverend_http' => $serverendhttp,
            'query_string' => $queryString,
            'query_string_url' => $queryStringUrl,
            'requested_full_url' => $fullRequestedURL,
            'platform' => $platform
        ];

        return $return;
    }

    static public function navData($nav_highlight)
    {
        $return = [];
        $return['highlight'] = $nav_highlight;

        if (isset($nav_highlight)) {
            $highlight = 0;
            $dim = 0;
            switch($nav_highlight) {
                case 'index':
                    $highlight = 1;
                    $dim = 0;
                    break;
                case 'cables':
                    $highlight = 2;
                    $dim = 1;
                    break;
                case 'assets':
                    $highlight = 3;
                    $dim = 1;
                    break;
                case 'containers':
                    $highlight = 4;
                    $dim = 1;
                    break;
                case 'admin':
                    $highlight = 5;
                    $dim = 1;
                    break;
                case 'favourites':
                    $highlight = 6;
                    $dim = 1;
                    break;
		        case 'profile':
                    $highlight = 7;
                    $dim = 1;
                    break;
                case 'about':
                    $dim = 1;
                    break;
                default:
                    $highlight = 0;
                    $dim = 0;
                    break;
            }
        } else {
            $highlight = 0;
            $dim = 0;
        }
        $return['highlight_num'] = $highlight;
        $return['button_dimming'] = $dim;

        return $return;
    }

    static public function getUser() 
    {
        if (!($user = Auth::user())) {
            return null;
        }

        $user_data = $user->toArray();
        $user_data['role_data'] = GeneralModel::getAllWhere('users_roles', ['id' => $user_data['role_id'] ?? 1])[0] ?? [];
        $user_data['theme_data'] = GeneralModel::getAllWhere('theme', ['id' => $user_data['theme_id'] ?? 1])[0] ?? [];

        return $user_data;
    }

    static public function sessionData() 
    {
        
    }
    
    static public function temp_activeUserData() 
    {
        $user_id = 0; // make this different

        $instance = new self();
        $instance->setTable('users_old AS users');

        $result = $instance->selectRaw('users.id AS user_id, users.username, users.first_name, 
                                        users.last_name, users.email, users.auth, users.role_id, 
                                        users.enabled, users.password_expired, users.theme_id, users.2fa_enabled,
                                        COALESCE(NULLIF(users.theme_id, 0), config.default_theme_id) AS final_theme_id,
                                        theme.name AS theme_name, theme.file_name AS theme_file_name,
                                        users_roles.name AS users_roles_name, users_roles.is_optic AS users_roles_is_optic,
                                        users_roles.is_admin AS users_roles_is_admin, users_roles.is_root AS users_roles_is_root')
                            ->where('users.id', '=', $user_id)
                            ->crossJoin('config') // Ensures `config.default_theme_id` is part of the query first
                            ->leftJoin('theme', \DB::raw('theme.id'), '=', \DB::raw('COALESCE(NULLIF(users.theme_id, 0), config.default_theme_id)'))
                            ->join('users_roles', 'users.role_id', '=', 'users_roles.id')
                            ->get()
                            ->toArray()[0];

        $user_data = [
                'id' => $result['user_id'],
                'username' => $result['username'],
                'first_name' => $result['first_name'],
                'last_name' => $result['last_name'],
                'email' => $result['email'],
                'auth' => $result['auth'],
                'role_id' => $result['role_id'],
                'role' => $result['users_roles_name'],
                'enabled' => $result['enabled'],
                'password_exipred' => $result['password_expired'],
                'theme_id' => $result['final_theme_id'],
                'theme_file_name' => $result['theme_file_name'],
                '2fa_enabled' => $result['2fa_enabled']
        ];

        return $user_data;
    }

    static public function updateChecker($versionNumber) 
    {
        function parseVersion($version) {
            $parts = explode('.', $version);
            return [
                'major' => isset($parts[0]) ? (int)$parts[0] : 0,
                'minor' => isset($parts[1]) ? (int)$parts[1] : 0,
                'patch' => isset($parts[2]) ? (int)$parts[2] : 0,
            ];
        }
        function checkUpdates($version) {
            $return = [];
            $version_file_path = 'version.json';
            // master gitlab branch head file
            $remoteHeadFileUrl =  'https://gitlab.com/andrewrichardson701/stockbase/-/raw/master/head.php';

            // check if version file exists, if not create a blank one
            if (!file_exists($version_file_path)) {
                file_put_contents($version_file_path, '');
            }

            $currentVersion = ltrim($version, 'v'); // Remove the leading 'v'

            $version_file_info = json_decode(file_get_contents($version_file_path), true);
            
            // set the current version in the array
            $version_file_info['current_version'] = $currentVersion;

            if (!isset($version_file_info['check_time']) || !isset($version_file_info['latest_version']) || $version_file_info['check_time'] < time() - (60*15)) { // 15 minutes interval
                // check for updates
                // Fetch the latest head.php content
                $remoteHeadContent = file_get_contents($remoteHeadFileUrl);
                if ($remoteHeadContent === false) {
                    $return['error'][] = "Could not retrieve the latest version information.";
                }

                // Extract and strip 'v' from the $version value in the fetched head.php content
                preg_match('/\$versionNumber\s*=\s*[\'"]v?([^\'"]+)[\'"]/', $remoteHeadContent, $matches);
                $latestVersion = isset($matches[1]) ? ltrim($matches[1], 'v') : null;

                $version_file_info['check_time'] = time();
                $version_file_info['latest_version'] = $latestVersion;
                
                file_put_contents($version_file_path, json_encode($version_file_info));
            } else {
                $latestVersion = $version_file_info['latest_version'];
            }

            

            if ($latestVersion === null) {
                $return['error'][] = "Failed to determine the latest version.";
            }

            if (!isset($return['error'])) {
                // Parse the current and latest versions
                $current = parseVersion($currentVersion);
                $latest = parseVersion($latestVersion);

                // Calculate the difference in each component, ignoring negative values
                $majorDifference = max($latest['major'] - $current['major'], 0);
                $minorDifference = max($latest['minor'] - $current['minor'], 0);
                $patchDifference = max($latest['patch'] - $current['patch'], 0);

                $return['message'][] = "You are using <or class='green'>v$currentVersion</or>.";
                // Generate the update message
                if ($majorDifference > 0 || $minorDifference > 0 || $patchDifference > 0) {
                    $return['update'] = 1;

                    $return['message'][] = "The latest version is <or class='green'>v$latestVersion</or>.";
                    
                    // Provide detailed message based on the differences
                    $return['message'][] = "<br>You are behind by:";
                    if ($majorDifference > 0) {
                        $return['message'][] = "&#8226; <or class='red'>$majorDifference</or> major release(s)";
                        $return['major'] = $majorDifference;
                    }
                    if ($minorDifference > 0) {
                        $return['message'][] = "&#8226; <or class='red'>$minorDifference</or> minor release(s)";
                        $return['minor'] = $minorDifference;
                    }
                    if ($patchDifference > 0) {
                        $return['message'][] = "&#8226; <or class='red'>$patchDifference</or> patch(es)";
                        $return['patch'] = $patchDifference;
                    }
                    
                    $return['message'][] = "<br>Please update to the latest version.";
                } else {
                    $return['update'] = 0;
                    $return['message'][] = "You are up to date!";
                }
            }

            $return['latest_version'] = $latestVersion;

            return $return;
        }

        $update_check = checkUpdates($versionNumber);
        $update_text = '';
        $update_available = -1;
        if (!array_key_exists('error', $update_check)) {
            if ($update_check['update'] == 1) {
                $update_available = 1;
            } else {
                $update_available = 0;
            }
            $messages = $update_check['message'];
            $m = 0;
            if (isset($messages)) {
                foreach($messages as $message) {
                    $m ++;
                    if ($m < count($messages)) {
                        $message .= '<br>';
                    }
                    $update_text .= $message;
                }
            }
        } else {
            // error exists
            $errors = $update_check['error'];
            $error_string = '<or class="red">';
            foreach ($errors as $error) {
                $error_string .= '&#8226; '.$error;
            }
            $error_string .= '</or>';
            $update_text = $error_string;
        }

        $latest_version = isset($update_check['latest_version']) ? $update_check['latest_version'] : null;
        $return = ['update_available' => $update_available, 
                    'update_text' => $update_text, 
                    'latest_version' => $latest_version];
        return $return;
    }

    static public function checkAreaSiteMatch($area, $site) 
    {
        if ($area !== 0 && $area !== '' && $area !== '0') {
            $instance = new self();
            $instance->setTable('area');

            $out = $instance->where('id', '=', $area)
                            ->where('site_id', '=', $site)
                            ->get()
                            ->toarray();
            
            if (!empty($out)) {
                
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    static public function checkShelfAreaMatch($shelf, $area) 
    {
        if ($shelf !== 0 && $shelf !== '' && $shelf !== '0') {
            $instance = new self();
            $instance->setTable('shelf');

            $out = $instance->where('id', '=', $shelf)
                            ->where('area_id', '=', $area)
                            ->get()
                            ->toarray();
            
            if (!empty($out)) {
                
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    static public function interpolatedQuery($query, $bindings) // used to put the query variables in the sql when printing the SQL query
    {
        // Escape bindings to prevent injection issues.
        $escapedBindings = array_map(function ($binding) {
            if (is_string($binding)) {
                return "'" . addslashes($binding) . "'";
            } elseif (is_null($binding)) {
                return 'NULL';
            }
            return $binding;
        }, $bindings);

        // Replace `?` with `%s` and interpolate.
        return vsprintf(str_replace('?', '%s', $query), $escapedBindings);
    }

    static public function updateChangelog($info) 
    {
        $timestamp = date("Y-m-d H:i:s");
        $user_id = $info['user']['id'];
        $username = $info['user']['username'];
        $action = $info['action'];
        $table = $info['table'];
        $record_id = $info['record_id'];
        $field = $info['field'];
        $new_value = $info['new_value'];

        $previous_info = (array) DB::table($table)->find($record_id);

        if ($info['previous_value'] !== null) {
            if ($info['previous_value'] !== '') {
                $previous_value = $info['previous_value'];
            } else {
                $previous_value = null;
            }
        } else {
            $previous_value = $previous_info[$field] == null ? null : $previous_info[$field];
        }

        DB::table('changelog')->insert([
            'timestamp' => $timestamp,
            'user_id' => $user_id,
            'user_username' => $username,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $record_id,
            'field_name' => $field,
            'value_old' => $previous_value,
            'value_new' => $new_value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return 1;
    }

    static public function updateTransactions($info)
    {
        $stock_id = $info['stock_id'];
        $shelf_id = $info['shelf_id'];
        $item_id = $info['item_id'];
        $type = $info['type'];
        $quantity = $info['quantity'];
        $price = $info['price'];
        $serial_number = $info['serial_number'];
        $reason = $info['reason'];
        $comments = $info['comments'];
        $username = $info['user']['username'];

        DB::table('changelog')->insert([
            'stock_id' => $stock_id,
            'item_id' => $item_id,
            'type' => $type,
            'quantity' => $quantity,
            'price' => $price,
            'serial_number' => $serial_number,
            'reason' => $reason,
            'comments' => $comments,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'username' => $username,
            'shelf_id' => $shelf_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return 1;
    }

    static public function newProperty($request) 
    {

    }

    static public function getFilesInDirectory($directory)
    {
        $path = public_path('img/stock'); // Get full path to the folder
        $files = File::files($path); // Get all files in the directory

        // Extract filenames without full paths
        $fileNames = array_map(fn($file) => $file->getFilename(), $files);

        return $fileNames;
    }

    static public function imageUpload($request)
    {
        // $request->validate([
        //     'file' => 'required|image|mimes:jpeg,png,jpg,gif,ico|max:10000',
        //     'stock_id' => 'required|integer',
        // ]);
    
        $file = $request->file('image');
        $stock_id = $request['id'];
        $timestamp = now()->format('YmdHis');
    
        // Create a unique filename
        $filename = "stock-{$stock_id}-img-{$timestamp}." . $file->getClientOriginalExtension();

        // Move to public/img/stock
        $destinationPath = public_path('img/stock');
        $file->move($destinationPath, $filename);
    
        // Save to DB
        $new_stock_img_id = DB::table('stock_img')->insertGetId([
            'stock_id' => $stock_id,
            'image' => $filename,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // update changelog
        $user = GeneralModel::getUser();
        $info = [
            'user' => $user,
            'table' => 'stock_img',
            'record_id' => $new_stock_img_id,
            'field' => 'image',
            'new_value' => $filename,
            'action' => 'New record',
            'previous_value' => '',
        ];
        GeneralModel::updateChangelog($info);
    }
}
