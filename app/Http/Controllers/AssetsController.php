<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\AboutModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;

class AssetsController extends Controller
{
    //
    static public function index(Request $request) {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);

        return view('assets', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                            ]);
    }
}
