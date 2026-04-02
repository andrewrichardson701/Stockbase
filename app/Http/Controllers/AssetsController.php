<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\IndexModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;
use App\Models\DiskModel;

class AssetsController extends Controller
{
    //
    static public function index(Request $request) {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        return view('assets', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                            ]);
    }

    static public function cpus(Request $request)
    {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        return view('cpus', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                            ]);
    }

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
        
        $tags = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag', 0));

        $disk_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_item'));
        $disk_caddies = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_caddy', 0, 'vendor'));
        $disk_capacities = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_capacity', 0, 'capacity'));
        $disk_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_speed', 0, 'speed'));
        $disk_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_type', 0, 'name'));
        $disk_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_vendor', 0, 'name'));

        $q_data = IndexModel::queryData($request); // query string data

        $site = $request['site'] ?? 0;
        $search = $request['search'] ?? null;
        $deleted = $request['deleted'] ?? 0;

        $sort = $request['sort'] ?? 'vendor';
        $rows = $request['rows'] ?? 20;
        $page = $request['page'] ?? 1;

        $add_form = $request['add_form'] ?? null;

        $disks_data = DiskModel::getDisks($request, $sort, $deleted, $rows, $page);

        $params = ['asset_type' => 'disks', 
                'page' => $page,
                'site' => $site,
                'search' => $search, 
                'deleted' => $deleted,
                'sort' => $sort,
                'rows' => $rows,
                'request' => $request,
                'add_form' => $add_form
            ];
                    //  dd($optic_vendors);       
        return view('disks', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,
                                'tags' => $tags,

                                'disks_data' => $disks_data,
                                'disk_items' => $disk_items,
                                'disk_caddies' => $disk_caddies,
                                'disk_capacities' => $disk_capacities,
                                'disk_speeds' => $disk_speeds,
                                'disk_types' => $disk_types,
                                'disk_vendors' => $disk_vendors,
                                
                                'q_data' => $q_data,
                                'params' => $params
                            ]);
    }

    static public function incomplete(Request $request)
    {
        return dd('incomplete page.');
    }
}
