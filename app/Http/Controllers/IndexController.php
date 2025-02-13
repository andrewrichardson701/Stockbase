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

class IndexController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'index'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        // $head_data = GeneralModel::headData();
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer', 0));
        $tags = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag', 0));
        $q_data = IndexModel::queryData($request); // query string data

        return view('index', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,
                                'manufacturers' => $manufacturers,
                                'tags' => $tags,
                                'q_data' => $q_data,
                            ]);
    }

    static public function test(Request $request)
    {
        $nav_highlight = 'admin'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);
        
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site'));
        $site_links = AdminModel::attributeLinks('area', 'site_id', null, 1);
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area'));
        $area_links = AdminModel::attributeLinks('shelf', 'area_id', null, 1);
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf'));
        $shelf_links = AdminModel::attributeLinks('item', 'shelf_id', null, 1);
        $location_colors = [
                            0 => ['site' => '#F4BB44', 'area' => '#FFE47A', 'shelf' => '#FFDEAD'],
                            1 => ['site' => '#6ABAD6', 'area' => '#99D4EF', 'shelf' => '#C1E9FC'],
                            'deleted' => '#7E1515'
                            ];
        
        $themes = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('theme'));
        
        $users = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_old')); //update this to the correct users table
        $user_roles = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('users_roles'));
        
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
        $optic_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type'));
        $optic_speed_links = AdminModel::attributeLinks('optic_item', 'speed_id', 'id, speed_id, model, serial_number', 1);
        $optic_connectors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type'));
        $optic_connector_links = AdminModel::attributeLinks('optic_item', 'connector_id', 'id, connector_id, model, serial_number', 1);
        $optic_distances = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type'));
        $optic_distance_links = AdminModel::attributeLinks('optic_item', 'distance_id', 'id, distance_id, model, serial_number', 1);
        
        $deleted_stock = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('stock', 1));
        
        $notifications = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('notifications'));

        $changelog = GeneralModel::formatArrayOnIdAndCount(AdminModel::getChangelog10());
        // $q_data = IndexModel::queryData($request); // query string data
                    //  dd($optic_vendors);       
        return view('test', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'site_links' => $site_links,
                                'areas' => $areas,
                                'area_links' => $area_links,
                                'shelves' => $shelves,
                                'shelf_links' => $shelf_links,
                                'themes' => $themes,
                                
                                'users' => $users,
                                'user_roles' => $user_roles,
                                
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

                                'changelog' => $changelog,
                                // 'q_data' => $q_data,
                            ]);
    }
}
