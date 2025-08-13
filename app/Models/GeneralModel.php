<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionsModel;
use App\Models\SessionModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneralModel query()
 * @mixin \Eloquent
 */
class GeneralModel extends Model
{
    protected $keyType = 'string'; // Tell Laravel the PK type is string
    //
    static public function versionNumber()
    {
        return "1.3.0L";
    }

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

    static public function allDistinct($table, $deleted=null, $orderby=null) 
    {
        // get the current config for the system from the config table
        $instance = new self();
        $instance->setTable($table);

        return $instance->distinct()
                        ->when($deleted !== null, function ($query) use ($deleted) {
                            $query->where('deleted', '=', $deleted);
                        })
                        ->orderBy($orderby ?? 'id')
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

    static public function getAllWhere($table, $params, $orderby = null, $orderdir = 'asc')
    {
        $instance = new self();
        $instance->setTable($table);

        $fields = Schema::getColumnListing($table);

        $query = $instance->newQuery(); // ✅ Start a new query builder

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query->where($key, $value); // ✅ Correctly applying `where()`
            }
        }

        return $query->orderBy($orderby ?? 'id', $orderdir) // ✅ Correctly chaining `orderBy()`
                    ->get()
                    ->toArray();
    }

    public static function getFirstWhere($table, $params=[])
    {
        $instance = new self();
        $instance->setTable($table);

        $query = $instance->newQuery();

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query->where($key, $value);
            }
        }

        $result = $query->first();

        return $result ? $result->toArray() : null;
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

    // currently unused
    static public function formatArrayOnId($array) 
    {
        $formatted = [];
        $formatted['rows'] = [];
        $i = 0;
        foreach ($array as $entry) { // format the data
            if(isset($entry['id'])){
                $formatted['rows'][$entry['id']] = $entry;
            } else {
                $formatted['rows'][$i] = $entry;
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

    // currently unused
    static public function formatArrayOnField($array, $field) 
    {
        $formatted = [];
        $formatted['rows'] = [];
        $i = 0;
        foreach ($array as $entry) { // format the data
            if(isset($entry[$field])){
                $formatted['rows'][$entry[$field]] = $entry;
            } else {
                $formatted['rows'][$i] = $entry;
            }
            $i++;
        }
        return $formatted;
    }

    // currently unused
    static public function formatArrayOnFieldAndCount($array, $field='id') 
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
            if (isset($entry[$field])) {
                $formatted['rows'][$entry[$field]] = $entry;
            } else {
                $formatted['rows'][$i] = $entry;
            }
            $i++;
        }
        
        return $formatted;
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

    public static function getURLQuery($url)
    {
        $parsed_url = parse_url($url); // Step 2: Parse the URL

        $query_params = [];
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params); // Step 3: Convert query string to array
        }

        return $query_params;
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

    static public function headData($request) 
    {
        // update all session activity
        SessionModel::activityUpdates();
        
        $head_data = [];

        $head_data['session'] = GeneralModel::getFirstwhere('sessions', ['id' => Session::getId()]);

        $head_data['default_config'] = GeneralModel::configDefault();
        $head_data['config'] = GeneralModel::config();
        $head_data['config_compare'] = GeneralModel::configCompare();
        $head_data['version_number'] = GeneralModel::versionNumber();
        $head_data['default_theme'] = isset($head_data['config']['default_theme_id']) ? GeneralModel::getThemeFileName($head_data['config']['default_theme_id']) : GeneralModel::getThemeFileName($head_data['default_config']['default_theme_id']);
        $head_data['default_theme_default'] = GeneralModel::getThemeFileName($head_data['default_config']['default_theme_id']);
        $head_data['requested_info'] = GeneralModel::requestedInfo();

        $head_data['previous_url'] = GeneralModel::previousURL();

        $head_data['update_data'] = GeneralModel::updateChecker($head_data['version_number']);
        
        $head_data['user'] = GeneralModel::getUser();

        // Impersonations
        $head_data['impersonation'] = [];
        if ($request->session()->has('impersonate_id')) {
            //impersonation is active
            $head_data['impersonation']['active'] = 1;
            $head_data['impersonation']['impersonate_id'] = $request->session()->get('impersonate_id');
            $head_data['impersonation']['impersonator_id'] = $request->session()->get('impersonator_id') ?? 0;
        } else {
            $head_data['impersonation']['active'] = 0;
        }
        

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
        $user_data['permissions'] = GeneralModel::getAllWhere('users_permissions', ['id' => $user_data['id']], 'id')[0] ?? [];
        $user_data['theme_data'] = GeneralModel::getAllWhere('theme', ['id' => $user_data['theme_id'] ?? 1])[0] ?? [];
        
        $assets_permissions = ['optics', 'cpus', 'memory', 'disks', 'psus', 'fans'];
        $user_data['permissions']['assets'] = 0;
        foreach ($assets_permissions as $permission) {
            if ($user_data['permissions'][$permission] == 1) {
                $user_data['permissions']['assets'] = 1;
            }
        }
        
        return $user_data;
    }

    // currently unused - this was to be used in the changelog for impersonation overrides
    static public function getUserData($impersonator_id)
    {
        $user = User::find($impersonator_id);
        return $user;
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
            $remoteHeadFileUrl =  'https://gitlab.com/andrewrichardson701/stockbase/-/raw/master/resources/views/head.blade.php';

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

    static public function getFilesInDirectory($directory)
    {
        $path = public_path($directory); // Get full path to the folder
        $files = File::files($path); // Get all files in the directory

        // Extract filenames without full paths
        $fileNames = array_map(fn($file) => $file->getFilename(), $files);

        return $fileNames;
    }

    static public function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    static public function getDbTableNames($filter = 1)
    {
        $exclusions = ['cache', 'cache_locks', 'failed_jobs', 'job_batches', 'jobs', 'migrations'];
        $tables = DB::select('SHOW TABLES');

        if ($filter == 0) {
            $table_names = array_map('current', $tables);
            return $table_names;
        } else {
            // Get the name of the column returned by SHOW TABLES (differs by DB name)
            $table_key = array_key_first((array) $tables[0]);

            // Map to a flat array of table names
            $table_names = array_map(function ($table) use ($table_key) {
                return $table->$table_key;
            }, $tables);

            // Filter out excluded table names
            $filtered_tables = array_filter($table_names, function ($table) use ($exclusions) {
                return !in_array($table, $exclusions);
            });

            // Optional: reindex the array
            $filtered_tables = array_values($filtered_tables);

            return $filtered_tables;
        }
        
    }

    public static function isValidCSSFile($filePath)
    {
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
    
        $allowedMimeTypes = ['text/css', 'text/plain']; // Allow text/plain just in case
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return false;
        }
    
        // Read contents and scan for PHP tags
        $contents = file_get_contents($filePath);
        if (preg_match('/<\?(php|=)?|\?>/i', $contents)) {
            // Contains PHP tags
            return false;
        }
    
        // (Optional) Additional check: block common dangerous content
        $blacklist = ['<script', 'base64_decode', 'eval(', 'shell_exec', 'system('];
        foreach ($blacklist as $danger) {
            if (stripos($contents, $danger) !== false) {
                return false;
            }
        }
    
        return true;
    }

    static public function getSiteAreaShelfData($shelf_id)
    {
        $shelf_data_obj = DB::table('shelf')->where('id', $shelf_id)->first();
        $shelf_data = $shelf_data_obj ? (array) $shelf_data_obj : [];

        if (!empty($shelf_data['area_id'])) {
            $area_data_obj = DB::table('area')->where('id', $shelf_data['area_id'])->first();
            $area_data = $area_data_obj ? (array) $area_data_obj : [];

            $site_data_obj = !empty($area_data['site_id']) ? DB::table('site')->where('id', $area_data['site_id'])->first() : null;
            $site_data = $site_data_obj ? (array) $site_data_obj : [];
        } else {
            $area_data = [];
            $site_data = [];
        }

        return [
            'shelf_data' => $shelf_data,
            'area_data'  => $area_data,
            'site_data'  => $site_data,
        ];
    }
}
