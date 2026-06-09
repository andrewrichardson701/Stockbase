<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GeneralModel;
use App\Models\IndexModel;
use App\Models\ResponseHandlingModel;
use App\Models\CpuModel;

class CpuController extends Controller
{
    static public function cpus(Request $request)
    {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $nav_highlight = 'assets'; // for the nav highlighting

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));

        $cpu_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_vendor', 0, 'name'));
        $cpu_models = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_model', 0, 'name'));
        $cpu_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_item'));
        $cpu_sockets = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('socket', 'cpu_model', 0));
        $cpu_core_counts = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('core_count', 'cpu_model', 0));
        $cpu_clock_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('clock_speed', 'cpu_model', 0));
        $cpu_families = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('cpu_family', 'cpu_model', 0));

        $q_data = IndexModel::queryData($request); // query string data

        $site = $request['site'] ?? 0;
        $area = $request['area'] ?? 0;
        $shelf = $request['shelf'] ?? 0;
        $search = $request['search'] ?? null;
        $deleted = $request['deleted'] ?? 0;

        $cpu_model = $request['model'] ?? 0;
        $cpu_vendor = $request['vendor'] ?? 0;
        $cpu_socket = $request['socket'] ?? 0;
        $cpu_core_count = $request['core_count'] ?? 0;
        $cpu_clock_speed = $request['clock_speed'] ?? 0;
        $cpu_family = $request['cpu_family'] ?? 0;

        $sort = $request['sort'] ?? 'vendor';
        $rows = isset($request['rows']) ? (int)$request['rows'] : (GeneralModel::getUser()['table_row_count'] ? GeneralModel::getUser()['table_row_count'] : 20);
        $page = $request['page'] ?? 1;

        $form_model = $request['form_model'] ?? null;
        $form_vendor = $request['form_vendor'] ?? 0;
        $form_socket = $request['form_socket'] ?? 0;
        $form_core_count = $request['form_core_count'] ?? 0;
        $form_clock_speed = $request['form_clock_speed'] ?? 0;
        $form_cpu_family = $request['form_cpu_family'] ?? 0;
        $form_site = $request['form_site'] ?? 0;
        $form_area = $request['form_area'] ?? 0;
        $form_shelf = $request['form_shelf'] ?? 0;

        $add_form = $request['add_form'] ?? null;

        $cpus_data = CpuModel::getCpus($request, $sort, $deleted, $rows, $page);

        $params = ['asset_type' => 'cpus', 
                'page' => $page,
                'site' => $site,
                'area' => $area,
                'shelf' => $shelf,
                'search' => $search, 
                'deleted' => $deleted,
                'sort' => $sort,
                'rows' => $rows,
                'request' => $request,

                'cpu_vendor' => $cpu_vendor,
                'cpu_model' => $cpu_model,
                'cpu_socket' => $cpu_socket,
                'cpu_core_count' => $cpu_core_count,
                'cpu_clock_speed' => $cpu_clock_speed,
                'cpu_family' => $cpu_family,

                'add_form' => $add_form,

                'form_model' => $form_model,
                'form_vendor' => $form_vendor,
                'form_socket' => $form_socket,
                'form_core_count' => $form_core_count,
                'form_clock_speed' => $form_clock_speed,
                'form_cpu_family' => $form_cpu_family,
                'form_site' => $form_site,
                'form_area' => $form_area,
                'form_shelf' => $form_shelf,
            ];

        $cpu_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('cpu_item'));

        return view('cpus', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,

                                'cpus_data' => $cpus_data,
                                'cpu_items' => $cpu_items,
                                'cpu_vendors' => $cpu_vendors,
                                'cpu_models' => $cpu_models,
                                'cpu_sockets' => $cpu_sockets,
                                'cpu_core_counts' => $cpu_core_counts,
                                'cpu_clock_speeds' => $cpu_clock_speeds,
                                'cpu_families' => $cpu_families,
                                
                                'q_data' => $q_data,
                                'params' => $params
                            ]);
    }

    static public function cpusAdd(Request $request)
    {
        $previous = GeneralModel::previousURL();
        $query = http_build_query(
                [
                    'form_model' => $request['model'] ?? '',
                    'form_vendor' => $request['vendor'] ?? '',
                    'form_site' => $request['site'] ?? '',
                    'form_area' => $request['area'] ?? '',
                    'form_shelf' => $request['shelf'] ?? '',
                ]
            );
        $url = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . $query;
                                   
        if (isset($request['add-cpu-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'serial' => 'string|nullable',
                    'model' => 'integer|required',
                    'vendor' => 'integer|required',
                    'shelf' => 'integer|required'
                ]);

                return CpuModel::addCpu($request->input());
            } else {
                return redirect($url)->with('error', 'CSRF missmatch');
            }
        }
        return redirect($url)->with('error', 'Unknown request');
    }

    static public function cpuSerialSearch(Request $request)
    {
        // search for matching serial numbers
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'serial' => 'string|required',
            ]);
            return response()->json(CpuModel::serialMatchChecker($request->input()));
        } else {
            return response()->json(['error' => 'CSRF token missmatch.']);
        }
    }

    static public function cpuEdit(Request $request)
    {
        // dd($request->input());
        if (isset($request['cpu-edit-submit'])) {
            if ($request['_token'] == csrf_token()) {
                $request->validate([
                    'id' => 'numeric|required',
                    'model_id' => 'integer|required',
                    'vendor_id' => 'integer|required',
                    'serial_number' => 'string|nullable',
                    'shelf_id' => 'integer|required'
                ]);
                // dd($request->input());
                return CpuModel::editCpu($request->input());
            } else {
                return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
            }
        }
        return redirect(GeneralModel::previousURL())->with('error', 'Unknown request');
    }
}
