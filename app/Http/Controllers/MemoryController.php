<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GeneralModel;
use App\Models\IndexModel;
use App\Models\ResponseHandlingModel;
use App\Models\MemoryModel;

class MemoryController extends Controller
{
    //
    static public function memory(Request $request)
    {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
        
        $memory_models = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('model', 'memory_item', 0));
        $memory_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_item'));
        $memory_generations = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_generation', 0, 'name'));
        $memory_capacities = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_capacity', 0, 'name'));
        $memory_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_speed', 0, 'name'));
        $memory_ecc_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_ecc_type', 0, 'name'));
        $memory_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_vendor', 0, 'name'));
        $memory_form_factors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('memory_form_factor', 0, 'name'));

        $q_data = IndexModel::queryData($request); // query string data

        $site = $request['site'] ?? 0;
        $area = $request['area'] ?? 0;
        $shelf = $request['shelf'] ?? 0;
        $search = $request['search'] ?? null;
        $deleted = $request['deleted'] ?? 0;

        $memory_ecc_type = $request['ecc_type'] ?? 0;
        $memory_generation = $request['generation'] ?? 0;
        $memory_capacity = $request['capacity'] ?? 0;
        $memory_speed = $request['speed'] ?? 0;
        $memory_form_factor = $request['form_factor'] ?? 0;
        $memory_vendor = $request['vendor'] ?? 0;

        $sort = $request['sort'] ?? 'vendor';
        $rows = isset($request['rows']) ? (int)$request['rows'] : (GeneralModel::getUser()['table_row_count'] ? GeneralModel::getUser()['table_row_count'] : 20);
        $page = $request['page'] ?? 1;

        $form_model = $request['form_model'] ?? null;
        $form_capacity = $request['form_capacity'] ?? null;
        $form_vendor = $request['form_vendor'] ?? 0;
        $form_ecc_type = $request['form_ecc_type'] ?? 0;
        $form_speed = $request['form_speed'] ?? 0;
        $form_generation  = $request['form_generation'] ?? 0;
        $form_site = $request['form_site'] ?? 0;
        $form_area = $request['form_area'] ?? 0;
        $form_shelf = $request['form_shelf'] ?? 0;
        $form_form_factor = $request['form_form_factor'] ?? 0;

        $add_form = $request['add_form'] ?? null;

        $memory_data = MemoryModel::getMemory($request, $sort, $deleted, $rows, $page);

        $params = ['asset_type' => 'memory', 
                'page' => $page,
                'site' => $site,
                'area' => $area,
                'shelf' => $shelf,
                'search' => $search, 
                'deleted' => $deleted,
                'sort' => $sort,
                'rows' => $rows,
                'request' => $request,

                'memory_ecc_type' => $memory_ecc_type,
                'memory_generation' => $memory_generation,
                'memory_capacity' => $memory_capacity,
                'memory_speed' => $memory_speed,
                'memory_form_factor' => $memory_form_factor,
                'memory_vendor' => $memory_vendor,

                'add_form' => $add_form,

                'form_model' => $form_model,
                'form_capacity' => $form_capacity,
                'form_vendor' => $form_vendor,
                'form_ecc_type' => $form_ecc_type,
                'form_speed' => $form_speed,
                'form_generation' => $form_generation,
                'form_site' => $form_site,
                'form_area' => $form_area,
                'form_shelf' => $form_shelf,
                'form_form_factor' => $form_form_factor,
            ];

        return view('memory', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,

                                'memory_data' => $memory_data,
                                'memory_items' => $memory_items,
                                'memory_generations' => $memory_generations,
                                'memory_capacities' => $memory_capacities,
                                'memory_speeds' => $memory_speeds,
                                'memory_ecc_types' => $memory_ecc_types,
                                'memory_vendors' => $memory_vendors,
                                'memory_models' => $memory_models,
                                'memory_form_factors' => $memory_form_factors,

                                'q_data' => $q_data,
                                'params' => $params
                            ]);
    }
    static public function memoryAdd(Request $request)
    {
        $previous = GeneralModel::previousURL();
        $query = http_build_query(
                [
                    'form_model' => $request['model'] ?? '',
                    'form_capacity' => $request['capacity'] ?? '',
                    'form_vendor' => $request['vendor'] ?? '',
                    'form_ecc_type' => $request['ecc_type'] ?? '',
                    'form_speed' => $request['speed'] ?? '',
                    'form_generation' => $request['generation'] ?? '',
                    'form_site' => $request['site'] ?? '',
                    'form_form_factor' => $request['form_factor'] ?? '',
                    'form_area' => $request['area'] ?? '',
                    'form_shelf' => $request['shelf'] ?? '',
                ]
            );
        $url = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . $query;
                             
        if (isset($request['add-memory-submit']) || isset($request['add-memory-multiple-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'serial' => 'string|nullable',
                    'model' => 'string|required',
                    'generation' => 'integer|required',
                    'vendor' => 'integer|required',
                    'ecc_type' => 'integer|required', 
                    'speed' => 'integer|required', 
                    'form_factor' => 'integer|required', 
                    'capacity' => 'integer|required', 
                    'shelf' => 'integer|required'
                ]);
                if ($request->has('add-memory-submit-multiple')) {
                    $request->merge(['multiple' => true]);
                }

                return MemoryModel::addMemory($request->input());
            } else {
                return redirect($url)->with('error', 'CSRF missmatch');
            }
        }
        return redirect($url)->with('error', 'Unknown request');
    }

    static public function memoryDelete(Request $request)
    {
        if (isset($request['memory-delete-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'integer|required',
                    'reason' => 'string|required'
                ]);
                return MemoryModel::deleteMemory($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }

    static public function memorySerialSearch(Request $request)
    {
        // search for matching serial numbers
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'serial' => 'string|required',
            ]);
            return response()->json(MemoryModel::serialMatchChecker($request->input()));
        } else {
            return response()->json(['error' => 'CSRF token missmatch.']);
        }
    }

    static public function memoryEdit(Request $request)
    {
        // dd($request->input());
        if (isset($request['memory-edit-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'numeric|required',
                    'model' => 'string|required',
                    'serial_number' => 'string|nullable',
                    'vendor_id' => 'integer|required',
                    'ecc_type_id' => 'integer|required', 
                    'speed_id' => 'integer|required', 
                    'generation_id' => 'integer|required',
                    'form_factor_id' => 'integer|required', 
                    'capacity_id' => 'integer|required', 
                    'shelf_id' => 'integer|required'
                ]);
                // dd($request->input());
                return MemoryModel::editMemory($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }

    static public function memoryRestore(Request $request) 
    {
        if (isset($request['memory-restore-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'integer|required',
                ]);
                return MemoryModel::restoreMemory($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }
}
