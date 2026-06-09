<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GeneralModel;
use App\Models\AssetsModel;
use App\Models\ResponseHandlingModel;

class AssetsController extends Controller
{
    //
    static public function index(Request $request) {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $assets = AssetsModel::getAssets();

        return view('assets', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'assets' => $assets
                            ]);
    }

    static public function incomplete(Request $request)
    {
        return dd('incomplete page.');
    }
}
