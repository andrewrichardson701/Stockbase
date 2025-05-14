<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\AboutModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;

class AboutController extends Controller
{
    //
    static public function index(Request $request) {
        $nav_highlight = 'about'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        return view('about', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                            ]);
    }
}
