<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\IndexModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;

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

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site'));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area'));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf'));
        
        $stock = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_item'));
        $tags = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('tag'));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer'));

        $disk_items = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_item'));
        $disk_caddies = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_caddy'));
        $disk_capacities = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_capacity'));
        $disk_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_speed'));
        $disk_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_type'));
        $disk_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('disk_vendor'));

        $q_data = IndexModel::queryData($request); // query string data
                    //  dd($optic_vendors);       
        return view('disks', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'areas' => $areas,
                                'shelves' => $shelves,
                                
                                'stock' => $stock,
                                'tags' => $tags,
                                'manufacturers' => $manufacturers,

                                'disk_items' => $disk_items,
                                'disk_caddies' => $disk_caddies,
                                'disk_capacities' => $disk_capacities,
                                'disk_speeds' => $disk_speeds,
                                'disk_types' => $disk_types,
                                'disk_vendors' => $disk_vendors,
                                
                                'q_data' => $q_data,
                            ]);
    }

    static public function incomplete(Request $request)
    {
        return dd('incomplete page.');
    }
}
