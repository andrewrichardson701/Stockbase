<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\ResponseHandlingModel;
use App\Models\AdminModel;
use App\Models\LdapModel;
use App\Models\SmtpModel;
use App\Models\ChangelogModel;
use App\Models\StockModel;
use App\Models\SessionModel;
use App\Models\WebhookModel;
use App\Models\SsoModel;

class AdminController extends Controller
{
    //
    static public function index(Request $request, $setting = null): View|RedirectResponse  
    {
        $nav_highlight = 'admin'; // for the nav highlighting
        
        if ($setting == null) {
            return redirect()->route('admin', ['setting' => 'global']);
        }

        $nav_data = GeneralModel::navData($nav_highlight);
        $query_section = $request->query('section') ?? null;
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request, $query_section);
        
        $view_array = [
            'nav_data' => $nav_data,
            'nav_secondary' => $setting,
            'response_handling' => $response_handling
        ];

        switch ($setting) {
            case 'global':
                $view_array['themes'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('theme'));
                break;
            case 'users':
                $view_array['users'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users')); //update this to the correct users table
                $view_array['users_permissions'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_permissions'));
                $view_array['users_permissions_roles'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_permissions_roles'));
                $view_array['active_sessions'] = GeneralModel::formatArrayOnIdAndCount(AdminModel::getActiveSessionLog());
                break;
            case 'authentication':
                // all based on the config in $head_data which is provided by middleware
                break;
            case 'image-management':
                $view_array['image_management_count'] = AdminModel::imageManagementCount();
                break;
            case 'stock-attributes':
                $view_array['stock'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock'));
                $view_array['tags'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag'));
                $view_array['tag_links'] = AdminModel::taggedStockByTagId();
                $view_array['manufacturers'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer'));
                $view_array['manufacturer_links'] = AdminModel::attributeLinks('item', 'manufacturer_id', null, 1);
                break;
            case 'optic-attributes':
                $view_array['optics'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_item'));
                $view_array['optic_vendors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_vendor'));
                $view_array['optic_vendor_links'] = AdminModel::attributeLinks('optic_item', 'vendor_id', 'id, vendor_id, model, serial_number', 1);
                $view_array['optic_types'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type'));
                $view_array['optic_type_links'] = AdminModel::attributeLinks('optic_item', 'type_id', 'id, type_id, model, serial_number', 1);
                $view_array['optic_speeds'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_speed'));
                $view_array['optic_speed_links'] = AdminModel::attributeLinks('optic_item', 'speed_id', 'id, speed_id, model, serial_number', 1);
                $view_array['optic_connectors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_connector'));
                $view_array['optic_connector_links'] = AdminModel::attributeLinks('optic_item', 'connector_id', 'id, connector_id, model, serial_number', 1);
                $view_array['optic_distances'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_distance'));
                $view_array['optic_distance_links'] = AdminModel::attributeLinks('optic_item', 'distance_id', 'id, distance_id, model, serial_number', 1);
                break;
            case 'cpu-attributes':
                $view_array['cpus'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_item'));
                $view_array['cpu_vendors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_vendor'));
                $view_array['cpu_vendor_links'] = AdminModel::attributeLinks('cpu_item', 'vendor_id', 'id, vendor_id, model_id, serial_number', 1);
                $view_array['cpu_models'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_model'));
                $view_array['cpu_model_links'] = AdminModel::attributeLinks('cpu_item', 'model_id', 'id, vendor_id, model_id, model_id, serial_number', 1);
                break;
            case 'memory-attributes':
                $view_array['memory'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_item'));
                $view_array['memory_vendors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_vendor'));
                $view_array['memory_vendor_links'] = AdminModel::attributeLinks('memory_item', 'vendor_id', 'id, vendor_id, model, serial_number', 1);
                $view_array['memory_generations'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_generation'));
                $view_array['memory_generation_links'] = AdminModel::attributeLinks('memory_item', 'generation_id', 'id, generation_id, model, serial_number', 1);
                $view_array['memory_ecc_types'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_ecc_type'));
                $view_array['memory_ecc_type_links'] = AdminModel::attributeLinks('memory_item', 'ecc_type_id', 'id, ecc_type_id, model, serial_number', 1);
                $view_array['memory_capacities'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_capacity'));
                $view_array['memory_capacity_links'] = AdminModel::attributeLinks('memory_item', 'capacity_id', 'id, capacity_id, model, serial_number', 1);
                $view_array['memory_form_factors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_form_factor'));
                $view_array['memory_form_factor_links'] = AdminModel::attributeLinks('memory_item', 'form_factor_id', 'id, form_factor_id, model, serial_number', 1);
                $view_array['memory_speeds'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_speed'));
                $view_array['memory_speed_links'] = AdminModel::attributeLinks('memory_item', 'speed_id', 'id, speed_id, model, serial_number', 1);
                break;
            case 'disk-attributes':
                $view_array['disks'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_item'));
                $view_array['disk_vendors'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_vendor'));
                $view_array['disk_vendor_links'] = AdminModel::attributeLinks('disk_item', 'vendor_id', 'id, vendor_id, model, serial_number', 1);
                $view_array['disk_types'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_type'));
                $view_array['disk_type_links'] = AdminModel::attributeLinks('disk_item', 'type_id', 'id, type_id, model, serial_number', 1);
                $view_array['disk_speeds'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_speed'));
                $view_array['disk_speed_links'] = AdminModel::attributeLinks('disk_item', 'speed_id', 'id, speed_id, model, serial_number', 1);
                $view_array['disk_capacities'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_capacity'));
                $view_array['disk_capacity_links'] = AdminModel::attributeLinks('disk_item', 'capacity_id', 'id, capacity_id, model, serial_number', 1);
                $view_array['disk_rpms'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_rpm'));
                $view_array['disk_rpm_links'] = AdminModel::attributeLinks('disk_item', 'rpm_id', 'id, rpm_id, model, serial_number', 1);
                $view_array['disk_caddies'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_caddy'));
                $view_array['disk_caddy_links'] = AdminModel::attributeLinks('disk_item', 'caddy_id', 'id, caddy_id, model, serial_number', 1);
                break;
            case 'stock-management':
                $view_array['deleted_stock'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock', 1));
                break;
            case 'stock-locations':
                $view_array['users_permissions_roles'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_permissions_roles'));
                $view_array['sites'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site'));
                $view_array['site_links'] = AdminModel::attributeLinks('area', 'site_id', null, 1);
                $view_array['site_links_optics'] = AdminModel::attributeLinks('optic_item', 'site_id', null, 1);
                $view_array['areas'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area'));
                $view_array['area_links'] = AdminModel::attributeLinks('shelf', 'area_id', null, 1);
                $view_array['shelves'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf'));
                $view_array['shelf_links'] = AdminModel::attributeLinks('item', 'shelf_id', null, 1);
                $view_array['shelf_links_containers'] = AdminModel::attributeLinks('container', 'shelf_id', null, 1);
                $view_array['location_colors'] = [
                            0 => ['site' => '#F4BB44', 'area' => '#FFE47A', 'shelf' => '#FFDEAD'],
                            1 => ['site' => '#6ABAD6', 'area' => '#99D4EF', 'shelf' => '#C1E9FC'],
                            'deleted' => '#7E1515'
                        ];
                break;
            case 'email':
                $view_array['email_notifications'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('email_notifications'));
                $view_array['email_templates'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('email_templates'));
                break;
            case 'webhook':
                $view_array['webhook_notifications'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('webhook_notifications'));
                $view_array['webhook_templates'] = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('webhook_templates'));
                break;
            case 'changelog':
                $view_array['changelog'] = GeneralModel::formatArrayOnIdAndCount(ChangelogModel::getChangelog(20));
                break;
            default:
                return redirect()->route('admin', ['setting' => 'global']);
        }

        return view('admin', $view_array);     
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
                // dd($request);
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

        return redirect()->to(route('admin', ['setting' => 'stock-attributes']) . '#attribute-settings')->with('error', 'Unknown selection');
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

        return redirect()->to(route('admin', ['setting' => 'stock-management']) . '#stockmanagement-settings')->with('error', 'Unknown selection');
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
                $validated = $request->validate([
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

                // set any nullable fields to null if empty
                $validated = array_merge(
                    array_fill_keys([
                        'smtp_client_id',
                        'smtp_client_secret',
                        'smtp_oauth_provider',
                        'smtp_refresh_token',
                        'smtp_username',
                        'smtp_password',
                    ], null),
                    $validated
                );

                return AdminModel::updateConfigSettings($validated);
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }
        return 'unknown request';
    }

    static public function webhookSettings(Request $request)
    {
        if (isset($request['webhook-toggle-submit'])) {
            if ($request['_token'] == csrf_token()) {
                if (isset($request['webhook-enabled']) && in_array($request['webhook-enabled'], ['on', 'off'])) {
                    $enabled = $request['webhook-enabled'];
                } else {
                    $enabled = 'off';
                }
                return WebhookModel::toggleWebhook($enabled);
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['webhook-submit']) || isset($request['webhook-restore-defaults'])) { 
           if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'webhook_type' => 'string|required',
                        'webhook_friendly_name' => 'string|required',
                        'webhook_url' => 'string|required',
                        'webhook_avatar_url' => 'string|required',
                        'webhook_display_name' => 'string|required',
                        'webhook_prefix_message' => 'string|nullable',
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

    static public function ssoToggle(Request $request)
    {
        if (isset($request['sso-toggle-submit'])) {
            if ($request['_token'] == csrf_token()) {
                if (isset($request['saml_enabled']) && in_array($request['saml_enabled'], ['on', 'off'])) {
                    $enabled = $request['saml_enabled'];
                } else {
                    $enabled = 'off';
                }
                return SsoModel::toggleSso($enabled);
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }

        if (isset($request['sso-submit']) || isset($request['sso-restore-defaults'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                        'saml_tenant_id' => 'string|required'
                ]);
                return AdminModel::updateConfigSettings($request->input());
            } else {
                return 'Error: CSRF token missmatch.';
            }
        }
        return 'unknown request';
    }

    static public function toggleEmailNotification(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'id' => 'integer|required',
                    'value' => 'integer|required',
            ]);
            AdminModel::toggleEmailNotification($request->input());
        } else {
            return 'Error: CSRF token missmatch.';
        }

        return 'error';
    }

    static public function toggleWebhookNotification(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                    'id' => 'integer|required',
                    'value' => 'integer|required',
            ]);
            AdminModel::toggleWebhookNotification($request->input());
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
                return redirect()->to(route('admin', ['setting' => 'stock-locations']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
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
                return redirect()->to(route('admin', ['setting' => 'stock-locations']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
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
                return redirect()->to(route('admin', ['setting' => 'stock-locations']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
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
                return redirect()->to(route('admin', ['setting' => 'stock-locations']) . '#stocklocations-settings')->with('error', 'CSRF missmatch');
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
                return redirect()->to(route('admin', ['setting' => 'image-management']) . '#imagemanagement-settings')->with('error', 'CSRF missmatch');
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

            return SessionModel::killSession($request['session_id']);
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

    static public function webhookTemplate(Request $request)
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
                return AdminModel::updateWebhookTemplate($request->input());
            } elseif ($request['submit'] == 'restore') {
                return AdminModel::restoreWebhookTemplate($request->input());
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
                return redirect()->to(route('admin', ['setting' => 'users']) . '#users-settings')->with('error', 'Password and password_confirm did not match.');
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
                'containers' => $request['permissions_containers'] ?? 'off',
                'changelog' => $request['permissions_changelog'] ?? 'off'
            ];
            
            return AdminModel::addLocalUser($user_data, $permissions_data);
            
        } else {
            return redirect()->to(route('admin', ['setting' => 'users']) . '#users-settings')->with('error', 'CSRF missmatch');
        }
    }

    static public function debug(Request $request)
    {
        $nav_highlight = 'admin'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $routes = Route::getRoutes();
        
        return view('debug', [
                            'nav_data' => $nav_data,
                            'response_handling' => $response_handling,
                            'routes' => $routes,
                            ]);
    }

}