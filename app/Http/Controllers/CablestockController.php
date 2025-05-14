<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\CablestockModel;
use App\Models\ResponseHandlingModel;

class CablestockController extends Controller
{
    //
    static public function index(Request $request): View|RedirectResponse  
    {
        $nav_highlight = 'cables'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $cable_types = GeneralModel::formatArrayOnIdAndCount(CablestockModel::getCableTypesByParent());
        $cables = GeneralModel::formatArrayOnIdAndCount(CablestockModel::getCablesByName());
        $q_data = CablestockModel::queryData($request); // query string data

        return view('cablestock', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites,
                                'cable_types' => $cable_types,
                                'cables' => $cables,
                                'q_data' => $q_data,
                            ]);
    }
}
