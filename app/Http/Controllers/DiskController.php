<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GeneralModel;
use App\Models\IndexModel;
use App\Models\ResponseHandlingModel;
use App\Models\DiskModel;

class DiskController extends Controller
{
    //
    static public function disks(Request $request)
    {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $nav_highlight = 'assets'; // for the nav highlighting

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        
        $disk_models = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('model', 'disk_item', 0));
        $disk_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_item'));
        $disk_caddies = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_caddy', 0, 'name'));
        $disk_capacities = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_capacity', 0, 'name'));
        $disk_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_speed', 0, 'name'));
        $disk_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_type', 0, 'name'));
        $disk_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_vendor', 0, 'name'));
        $disk_rpms = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_rpm', 0, 'name'));

        $q_data = IndexModel::queryData($request); // query string data

        $site = $request['site'] ?? 0;
        $area = $request['area'] ?? 0;
        $shelf = $request['shelf'] ?? 0;
        $search = $request['search'] ?? null;
        $deleted = $request['deleted'] ?? 0;

        $disk_type = $request['type'] ?? 0;
        $disk_speed = $request['speed'] ?? 0;
        $disk_capacity = $request['capacity'] ?? 0;
        $disk_caddy = $request['caddy'] ?? 0;
        $disk_form_factor = $request['form_factor'] ?? 0;
        $disk_ssd = $request['ssd'] ?? '';
        $disk_vendor = $request['vendor'] ?? 0;
        $disk_destroy = $request['destroy'] ?? '';
        $disk_rpm = $request['rpm'] ?? 0;

        $sort = $request['sort'] ?? 'vendor';
        $rows = isset($request['rows']) ? (int)$request['rows'] : (GeneralModel::getUser()['table_row_count'] ? GeneralModel::getUser()['table_row_count'] : 20);
        $page = $request['page'] ?? 1;

        $form_model = $request['form_model'] ?? null;
        $form_capacity = $request['form_capacity'] ?? null;
        $form_vendor = $request['form_vendor'] ?? 0;
        $form_type = $request['form_type'] ?? 0;
        $form_speed = $request['form_speed'] ?? 0;
        $form_caddy  = $request['form_caddy'] ?? 0;
        $form_site = $request['form_site'] ?? 0;
        $form_area = $request['form_area'] ?? 0;
        $form_shelf = $request['form_shelf'] ?? 0;
        $form_ssd = $request['form_ssd'] ?? '';
        $form_form_factor = $request['form_form_factor'] ?? 0;
        $form_destroy = $request['form_destroy'] ?? '';
        $form_rpm = $request['form_rpm'] ?? 0;

        $add_form = $request['add_form'] ?? null;

        $disks_data = DiskModel::getDisks($request, $sort, $deleted, $rows, $page);

        $params = ['asset_type' => 'disks', 
                'page' => $page,
                'site' => $site,
                'area' => $area,
                'shelf' => $shelf,
                'search' => $search, 
                'deleted' => $deleted,
                'sort' => $sort,
                'rows' => $rows,
                'request' => $request,

                'disk_type' => $disk_type,
                'disk_speed' => $disk_speed,
                'disk_capacity' => $disk_capacity,
                'disk_caddy' => $disk_caddy,
                'disk_form_factor' => $disk_form_factor,
                'disk_ssd' => $disk_ssd,
                'disk_vendor' => $disk_vendor, 
                'disk_destroy' => $disk_destroy,
                'disk_rpm' => $disk_rpm,

                'add_form' => $add_form,

                'form_model' => $form_model,
                'form_capacity' => $form_capacity,
                'form_vendor' => $form_vendor,
                'form_type' => $form_type,
                'form_speed' => $form_speed,
                'form_caddy' => $form_caddy,
                'form_site' => $form_site,
                'form_ssd' => $form_ssd,
                'form_form_factor' => $form_form_factor,
                'form_destroy' => $form_destroy,
                'form_rpm' => $form_rpm,
                'form_area' => $form_area,
                'form_shelf' => $form_shelf,
            ];
                    ;       
        return view('disks', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,

                                'disks_data' => $disks_data,
                                'disk_items' => $disk_items,
                                'disk_caddies' => $disk_caddies,
                                'disk_capacities' => $disk_capacities,
                                'disk_speeds' => $disk_speeds,
                                'disk_types' => $disk_types,
                                'disk_vendors' => $disk_vendors,
                                'disk_models' => $disk_models,
                                'disk_rpms' => $disk_rpms,
                                
                                'q_data' => $q_data,
                                'params' => $params
                            ]);
    }

    static public function diskAdd(Request $request)
    {
        $previous = GeneralModel::previousURL();
        $query = http_build_query(
                [
                    'form_model' => $request['model'] ?? '',
                    'form_capacity' => $request['capacity'] ?? '',
                    'form_vendor' => $request['vendor'] ?? '',
                    'form_type' => $request['type'] ?? '',
                    'form_speed' => $request['speed'] ?? '',
                    'form_caddy' => $request['caddy'] ?? '',
                    'form_site' => $request['site'] ?? '',
                    'form_ssd' => $request['ssd'] ?? '',
                    'form_form_factor' => $request['form_factor'] ?? '',
                    'form_destroy' => $request['destroy'] ?? '',
                    'form_rpm' => $request['rpm'] ?? '',
                    'form_area' => $request['area'] ?? '',
                    'form_shelf' => $request['shelf'] ?? '',
                ]
            );
        $url = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . $query;
                                   
        if (isset($request['add-disk-submit']) || isset($request['add-disk-submit-multiple'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'serial' => 'string|required',
                    'model' => 'string|required',
                    'caddy' => 'integer|required',
                    'vendor' => 'integer|required',
                    'type' => 'integer|required', 
                    'speed' => 'integer|required', 
                    'form_factor' => 'string|required', 
                    'destroy' => 'integer|nullable', 
                    'ssd' => 'integer|required', 
                    'rpm' => 'integer|required',
                    'capacity' => 'integer|required', 
                    'shelf' => 'integer|required'
                ]);
                if ($request->has('add-disk-submit-multiple')) {
                    $request->merge(['multiple' => true]);
                }

                return DiskModel::addDisk($request->input());
            } else {
                return redirect($url)->with('error', 'CSRF missmatch');
            }
        }
        return redirect($url)->with('error', 'Unknown request');
    }

    static public function diskDelete(Request $request)
    {
        if (isset($request['disk-delete-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'integer|required',
                    'reason' => 'string|required'
                ]);
                return DiskModel::deleteDisk($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }

    static public function diskRestore(Request $request) 
    {
        if (isset($request['disk-restore-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'integer|required',
                ]);
                return DiskModel::restoreDisk($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }

    static public function diskSerialSearch(Request $request)
    {
        // search for matching serial numbers
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'serial' => 'string|required',
            ]);
            return response()->json(DiskModel::serialMatchChecker($request->input()));
        } else {
            return response()->json(['error' => 'CSRF token missmatch.']);
        }
    }

    static public function diskEdit(Request $request)
    {
        // dd($request->input());
        if (isset($request['disk-edit-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'numeric|required',
                    'model' => 'string|required',
                    'serial_number' => 'string|required',
                    'caddy_id' => 'integer|required',
                    'vendor_id' => 'integer|required',
                    'type_id' => 'integer|required', 
                    'speed_id' => 'integer|required', 
                    'rpm_id' => 'integer|required',
                    'form_factor' => 'string|required', 
                    'destroy' => 'integer|nullable', 
                    'ssd' => 'integer|required', 
                    'capacity_id' => 'integer|required', 
                    'shelf_id' => 'integer|required'
                ]);
                // dd($request->input());
                return DiskModel::editDisk($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }
}
