<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;
use App\Models\CablestockModel;
use App\Models\AdminModel;
use App\Models\LdapModel;
use App\Models\SmtpModel;
use App\Models\ChangelogModel;
use App\Models\StockModel;
use App\Models\SessionModel;

class AdminController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'admin'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $query_section = $request->query('section') ?? null;
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request, $query_section);
        
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site'));
        $site_links = AdminModel::attributeLinks('area', 'site_id', null, 1);
        $site_links_optics = AdminModel::attributeLinks('optic_item', 'site_id', null, 1);
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area'));
        $area_links = AdminModel::attributeLinks('shelf', 'area_id', null, 1);
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf'));
        $shelf_links = AdminModel::attributeLinks('item', 'shelf_id', null, 1);
        $shelf_links_containers = AdminModel::attributeLinks('container', 'shelf_id', null, 1);

        $location_colors = [
                            0 => ['site' => '#F4BB44', 'area' => '#FFE47A', 'shelf' => '#FFDEAD'],
                            1 => ['site' => '#6ABAD6', 'area' => '#99D4EF', 'shelf' => '#C1E9FC'],
                            'deleted' => '#7E1515'
                            ];
        
        $themes = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('theme'));
        
        $users = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users')); //update this to the correct users table
        $users_permissions = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_permissions'));
        $users_permissions_roles = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_permissions_roles'));
        
        $active_sessions = GeneralModel::formatArrayOnIdAndCount(AdminModel::getActiveSessionLog());
        
        $image_management_count = AdminModel::imageManagementCount();
        
        $stock = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock'));
        $tags = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag'));
        $tag_links = AdminModel::taggedStockByTagId();
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer'));
        $manufacturer_links = AdminModel::attributeLinks('item', 'manufacturer_id', null, 1);
        
        $optics = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_item'));
        $optic_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_vendor'));
        $optic_vendor_links = AdminModel::attributeLinks('optic_item', 'vendor_id', 'id, vendor_id, model, serial_number', 1);
        $optic_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type'));
        $optic_type_links = AdminModel::attributeLinks('optic_item', 'type_id', 'id, type_id, model, serial_number', 1);
        $optic_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_speed'));
        $optic_speed_links = AdminModel::attributeLinks('optic_item', 'speed_id', 'id, speed_id, model, serial_number', 1);
        $optic_connectors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_connector'));
        $optic_connector_links = AdminModel::attributeLinks('optic_item', 'connector_id', 'id, connector_id, model, serial_number', 1);
        $optic_distances = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_distance'));
        $optic_distance_links = AdminModel::attributeLinks('optic_item', 'distance_id', 'id, distance_id, model, serial_number', 1);
        
        $deleted_stock = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock', 1));
        
        $notifications = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('notifications'));
        $email_templates = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('email_templates'));

        $changelog = GeneralModel::formatArrayOnIdAndCount(ChangelogModel::getChangelog(10));
        // $q_data = IndexModel::queryData($request); // query string data
                    //  dd($optic_vendors);       
        return view('admin', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'site_links' => $site_links,
                                'site_links_optics' => $site_links_optics,
                                'areas' => $areas,
                                'area_links' => $area_links,
                                'shelves' => $shelves,
                                'shelf_links' => $shelf_links,
                                'shelf_links_containers' => $shelf_links_containers,
                                'themes' => $themes,
                                
                                'users' => $users,
                                'users_permissions' => $users_permissions,
                                'users_permissions_roles' => $users_permissions_roles,
                                
                                'active_sessions' => $active_sessions,
                                
                                'image_management_count' => $image_management_count,
                                
                                'stock' => $stock,
                                'tags' => $tags,
                                'tag_links' => $tag_links,
                                'manufacturers' => $manufacturers,
                                'manufacturer_links' => $manufacturer_links,
                                
                                'optics' => $optics,
                                'optic_vendors' => $optic_vendors,
                                'optic_vendor_links' => $optic_vendor_links,
                                'optic_types' => $optic_types,
                                'optic_type_links' => $optic_type_links,
                                'optic_speeds' => $optic_speeds,
                                'optic_speed_links' => $optic_speed_links,
                                'optic_connectors' => $optic_connectors,
                                'optic_connector_links' => $optic_connector_links,
                                'optic_distances' => $optic_distances,
                                'optic_distance_links' => $optic_distance_links,
                                
                                'deleted_stock' => $deleted_stock,
                                'location_colors' => $location_colors,

                                'notifications' => $notifications,
                                'email_templates' => $email_templates,

                                'changelog' => $changelog,
                                // 'q_data' => $q_data,
                            ]);
    }

    static public function updateConfigSettings(Request $request)
    {
        if ($request['_token'] == csrf_token()) {

            try {
                $validated = $request->validate([
                    'system_name' => 'string|nullable',
                    'banner_color' => 'string|nullable',
                    'currency' => ['nullable', 'regex:/^.{1}$/u'],
                    'sku_prefix' => 'string|nullable',
                    'base_url' => 'string|nullable',
                    'default_theme_id' => 'integer|nullable',
                    'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'favicon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                dd($e->errors()); // show validation issues
            }

            return AdminModel::updateConfigSettings($request->all());
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function toggleFooter(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'type' => 'integer|required',
                    'value' => 'string|required',
            ]);
            AdminModel::toggleFooter($request->input());
        } else {
            return 'error';
        }
    }

    static public function toggleAuth(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'id' => 'string|required',
                    'value' => 'integer|required',
            ]);
            AdminModel::toggleAuth($request->input());
        } else {
            return 'error';
        }
    }

    static public function userSettings(Request $request) 
    {
        if (isset($request['user-permissions-submit'])) {
            // dd ($request->toArray());
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'user_id' => 'integer|required',
                        'admin' => 'string|nullable',
                        'locations' => 'string|nullable',
                        'stock' => 'string|nullable',
                        'cables' => 'string|nullable',
                        'optics' => 'string|nullable',
                        'cpus' => 'string|nullable',
                        'memory' => 'string|nullable',
                        'disks' => 'string|nullable',
                        'fans' => 'string|nullable',
                        'psus' => 'string|nullable',
                        'containers' => 'string|nullable',
                        'changelog' => 'string|nullable',
                ]);
                return AdminModel::userPermissionsChange($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        } 
        
        if (isset($request['user_enabled_submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'user_id' => 'integer|required',
                        'user_new_enabled' => 'integer|required',
                ]);
                return AdminModel::userEnabled($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        } 

        if (isset($request['user_permissions_preset_ajax'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'id' => 'integer|required',
                ]);
                return response()->json(AdminModel::getPermissionPreset($request->input('id')));
            } else {
                echo 'Error: CSRF token missmatch.';
            }
        } 


        if (isset($request['user_permissions_preset_add'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'name' => 'string|required',
                ]);
                return AdminModel::addPermissionPreset($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        } 

        if (isset($request['admin_pwreset_submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'user_id' => 'integer|required',
                        'password' => 'string|required',
                ]);
                return AdminModel::forcePasswordReset($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }  
        if (isset($request['reset_2fa_submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'user_id' => 'integer|required',
                ]);
                return AdminModel::force2FAReset($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }
    
        return 'Error: Unknown setting';
        
    }

    static public function attributeSettings(Request $request)
    {
        if (isset($request['attributemanagement-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'id' => 'integer|required',
                        'attribute-type' => 'string|required'
                ]);
                return AdminModel::attributeDelete($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['attributemanagement-restore'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'id' => 'integer|required',
                        'attribute-type' => 'string|required'
                ]);
                return AdminModel::attributeRestore($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        return redirect()->to(route('admin', ['section' => 'attribute-settings']) . '#attribute-settings')->with('error', 'Unknown selection');
    }

    static public function stockManagementSettings(Request $request)
    {
        if (isset($request['cost-toggle'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'type' => 'string|required',
                        'value' => 'integer|required'
                ]);
                return AdminModel::toggleCost($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['stockmanagement-restore'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'stockmanagement-type' => 'string|required',
                        'id' => 'integer|required'
                ]);
                return StockModel::restoreStock($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        return redirect()->to(route('admin', ['section' => 'stockmanagement-settings']) . '#stockmanagement-settings')->with('error', 'Unknown selection');
    }

    static public function smtpSettings(Request $request)
    {
        if (isset($request['smtp-toggle-submit'])) {
            if ($request['_token'] == csrf_token()) {
                if (isset($request['smtp-enabled']) && in_array($request['smtp-enabled'], ['on', 'off'])) {
                    $enabled = $request['smtp-enabled'];
                } else {
                    $enabled = 'off';
                }
                return SmtpModel::toggleSmtp($enabled);
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['smtp-submit']) || isset($request['smtp-restore-defaults'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'smtp_host' => 'string|required',
                        'smtp_port' => 'integer|required',
                        'smtp_encryption' => 'string|required',
                        'smtp_auth_type' => 'string|required',
                        'smtp_client_id' => 'string|nullable',
                        'smtp_client_secret' => 'string|nullable',
                        'smtp_oauth_provider' => 'string|nullable',
                        'smtp_refresh_token' => 'string|nullable',
                        'smtp_username' => 'string|nullable',
                        'smtp_password' => 'string|nullable',
                        'smtp_from_email' => 'string|required',
                        'smtp_from_name' => 'string|required',
                        'smtp_to_email' => 'string|required',
                ]);
                return AdminModel::updateConfigSettings($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }
        return 'unknown request';
    }

    static public function ldapSettings(Request $request)
    {
        if (isset($request['ldap-toggle-submit'])) {
            if ($request['_token'] == csrf_token()) {
                if (isset($request['ldap_enabled']) && in_array($request['ldap_enabled'], ['on', 'off'])) {
                    $enabled = $request['ldap_enabled'];
                } else {
                    $enabled = 'off';
                }
                return LdapModel::toggleLdap($enabled);
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['ldap-submit']) || isset($request['ldap-restore-defaults'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'ldap_username' => 'string|required',
                        'ldap_password' => 'string|required',
                        'ldap_domain' => 'string|required',
                        'ldap_host' => 'string|required',
                        'ldap_host_secondary' => 'string|nullable',
                        'ldap_port' => 'integer|required',
                        'ldap_basedn' => 'string|nullable',
                        'ldap_usergroup' => 'string|nullable',
                        'ldap_userfilter' => 'string|nullable',
                ]);
                return AdminModel::updateConfigSettings($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }
        return 'unknown request';
    }

    static public function toggleNotification(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'id' => 'integer|required',
                    'value' => 'integer|required',
            ]);
            AdminModel::toggleNotification($request->input());
        } else {
            return 'Error: CSRF token missmatch.';
        }

        return 'error';
    }

    static public function stockLocationSettings(Request $request)
    {
        if (isset($request['location-edit-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'type' => 'string|required',
                        'id' => 'integer|required',
                        'name' => 'string|required',
                        'description' => 'string|nullable',
                        'site' => 'integer|nullable',
                        'area' => 'integer|nullable',
                ]);
                return AdminModel::stockLocationEdit($request->input());
            } else {
                return redirect()->to(route('admin', ['section' => 'stocklocations-settings']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
            }
        }

        if (isset($request['location-delete-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'type' => 'string|required',
                        'id' => 'integer|required',
                ]);
                return AdminModel::stockLocationDelete($request->input());
            } else {
                return redirect()->to(route('admin', ['section' => 'stocklocations-settings']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
            }
        }

        if (isset($request['location-restore-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'type' => 'string|required',
                        'id' => 'integer|required',
                ]);
                return AdminModel::stockLocationRestore($request->input());
            } else {
                return redirect()->to(route('admin', ['section' => 'stocklocations-settings']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
            }
        }

        if (isset($request['location-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'type' => 'string|required',
                        'name' => 'string|required',
                        'description' => 'string|nullable',
                        'parent' => 'integer|nullable',
                ]);
                return AdminModel::stockLocationAdd($request->input());
            } else {
                return redirect()->to(route('admin', ['section' => 'stocklocations-settings']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
            }
        }
        
        return 'error';
    }

    static public function imageManagementSettings(Request $request)
    {
        if (isset($request['request_stock_images'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'current_page' => 'integer|required',
                        'page' => 'integer|required',
                ]);
                return AdminModel::imageManagementLoad($request->input());
            } else {
                return 'Error: CSRF Missmatch';
            }
        }

        if (isset($request['imagemanagement-delete-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'file-name' => 'string|required',
                        'file-links' => 'integer|required',
                ]);
                return AdminModel::imageManagementDelete($request->input());
            } else {
                return redirect()->to(route('admin', ['section' => 'imagemanagement-settings']) . '#imagemanagement-settings')->with('error', 'CSRF missmatch');
            }
        }
        
        return 'error';
    }

    static public function killUserSession(Request $request) 
    {
        // dd ($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'session_id' => 'string|required',
            ]);

            return SessionModel::killSession($request['seesion_id']);
        } else {
            return 'Error: CSRF Missmatch';
        }
        
    }

    static public function emailTemplate(Request $request)
    {
        // dd($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'template_id' => 'integer|required',
                    'slug' => 'string|required',
                    'subject' => 'string|required',
                    'body' => 'string|required',
                    'submit' => 'string|required',
            ]);
            if ($request['submit'] == 'update') {
                return AdminModel::updateEmailTemplate($request->input());
            } elseif ($request['submit'] == 'restore') {
                return AdminModel::restoreEmailTemplate($request->input());
            } else {
                return 'Error: Unknown submission type.';
            }
            
        } else {
            return 'Error: CSRF Missmatch';
        }
    }

    static public function addLocalUser(Request $request)
    {
        // dd($request->input());
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'name' => 'string|required',
                    'username' => 'string|required',
                    'email' => 'email|required',
                    'password' => 'string|required',
                    'password_confirm' => 'string|required',
            ]);

            if ($request['password'] !== $request['password_confirm']) {
                return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'Password and password_confirm did not match.');
            }
            
            $user_data = [
                'name' => $request['name'],
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => $request['password'],
            ];

            $permissions_data = [
                'root' => 'off',
                'admin' => $request['permission_admin'] ?? 'off',
                'locations' => $request['permission_locations'] ?? 'off',
                'stock' => $request['permission_stock'] ?? 'off',
                'cables' => $request['permissions_cables'] ?? 'off',
                'optics' => $request['permissions_optics'] ?? 'off',
                'cpus' => $request['permissions_cpus'] ?? 'off',
                'memory' => $request['permissions_memory'] ?? 'off',
                'disks' => $request['permissions_disks'] ?? 'off',
                'fans' => $request['permissions_fans'] ?? 'off',
                'psus' => $request['permissions_psus'] ?? 'off',
                'containers' => $request['permissions_containers'] ?? 'off',
                'changelog' => $request['permissions_changelog'] ?? 'off'
            ];
            
            return AdminModel::addLocalUser($user_data, $permissions_data);
            
        } else {
            return redirect()->to(route('admin', ['section' => 'users-settings']) . '#users-settings')->with('error', 'CSRF missmatch');
        }
    }

}